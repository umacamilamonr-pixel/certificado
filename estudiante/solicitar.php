<?php

// ==========================================
// INICIAR SESIÓN
// ==========================================

session_start();


// ==========================================
// CONEXIÓN A LA BASE DE DATOS
// ==========================================

require_once "../config/conexion.php";


// ==========================================
// VERIFICAR QUE LLEGUE EL ID DEL ESTUDIANTE
// ==========================================

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: index.php");
    exit();

}

$id_estudiante = intval($_POST["id_estudiante"] ?? 0);


if ($id_estudiante <= 0) {

    header("Location: index.php");
    exit();

}


// ==========================================
// BUSCAR ESTUDIANTE
// ==========================================

$sql = "SELECT
            id_estudiante,
            documento,
            nombre_completo,
            curso,
            jornada,
            estado
        FROM estudiantes
        WHERE id_estudiante = ?
        LIMIT 1";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("i", $id_estudiante);

$stmt->execute();

$resultado = $stmt->get_result();


// ==========================================
// VERIFICAR QUE EL ESTUDIANTE EXISTA
// ==========================================

if ($resultado->num_rows == 0) {

    $stmt->close();

    header("Location: index.php");
    exit();

}

$estudiante = $resultado->fetch_assoc();

$stmt->close();


// ==========================================
// VERIFICAR QUE EL ESTUDIANTE ESTÉ ACTIVO
// ==========================================

if ($estudiante["estado"] != "ACTIVO") {

    header("Location: index.php");
    exit();

}


// ==========================================
// COMPROBAR SI YA EXISTE UNA SOLICITUD
// PENDIENTE
// ==========================================

$sql_verificar = "SELECT
                    id_solicitud,
                    fecha_solicitud,
                    fecha_entrega
                  FROM solicitudes
                  WHERE id_estudiante = ?
                  AND estado = 'PENDIENTE'
                  LIMIT 1";

$stmt_verificar = $conexion->prepare($sql_verificar);

$stmt_verificar->bind_param("i", $id_estudiante);

$stmt_verificar->execute();

$resultado_verificar = $stmt_verificar->get_result();


// ==========================================
// SI YA EXISTE UNA SOLICITUD
// ==========================================

if ($resultado_verificar->num_rows > 0) {

    $solicitud_existente = $resultado_verificar->fetch_assoc();

    $stmt_verificar->close();

    ?>

    <!DOCTYPE html>

    <html lang="es">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport"
              content="width=device-width, initial-scale=1.0">

        <title>Solicitud en trámite</title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
        >

        <link
            rel="stylesheet"
            href="../assets/css/estilos.css"
        >

    </head>

    <body>


        <!-- ==========================================
             NAVBAR
        ========================================== -->

        <nav class="navbar navbar-dark bg-primary">

            <div class="container">

                <span class="navbar-brand">

                    Sistema de Certificados Escolares

                </span>

            </div>

        </nav>


        <!-- ==========================================
             CONTENIDO
        ========================================== -->

        <div class="container py-5">

            <div class="row justify-content-center">

                <div class="col-md-8 col-lg-6">

                    <div class="card shadow-sm">

                        <div class="card-body p-4 text-center">


                            <div class="alert alert-warning">

                                <h4 class="alert-heading">

                                    Certificado en trámite

                                </h4>

                                <hr>

                                <p>

                                    El estudiante

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $estudiante["nombre_completo"]
                                        );
                                        ?>
                                    </strong>

                                    ya tiene una solicitud
                                    de certificado pendiente.

                                </p>

                                <p class="mb-0">

                                    No es posible realizar otra
                                    solicitud en este momento.

                                </p>

                            </div>


                            <?php if (!empty($solicitud_existente["fecha_entrega"])): ?>

                                <p class="mt-3">

                                    Fecha disponible para reclamar:

                                    <strong>

                                        <?php
                                        echo date(
                                            "d/m/Y",
                                            strtotime(
                                                $solicitud_existente["fecha_entrega"]
                                            )
                                        );
                                        ?>

                                    </strong>

                                </p>

                            <?php endif; ?>


                            <a
                                href="index.php"
                                class="btn btn-primary mt-3"
                            >

                                Volver

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ==========================================
             FOOTER
        ========================================== -->

        <footer class="footer mt-4 text-center">

            <p>

                Sistema de Solicitud de Certificados Escolares

            </p>

        </footer>


    </body>

    </html>

    <?php

    exit();

}


$stmt_verificar->close();


// ==========================================
// CALCULAR 3 DÍAS HÁBILES
// ==========================================

$fecha_solicitud = new DateTime();

$fecha_entrega = clone $fecha_solicitud;

$dias_habiles = 0;


while ($dias_habiles < 3) {

    // Avanzar un día
    $fecha_entrega->modify("+1 day");

    // Obtener día de la semana
    // 1 = lunes
    // 7 = domingo

    $dia_semana = (int)$fecha_entrega->format("N");


    // Si no es sábado ni domingo
    if ($dia_semana < 6) {

        $dias_habiles++;

    }

}


// ==========================================
// CONVERTIR FECHA A FORMATO MYSQL
// ==========================================

$fecha_solicitud_mysql =
    $fecha_solicitud->format("Y-m-d H:i:s");

$fecha_entrega_mysql =
    $fecha_entrega->format("Y-m-d");


// ==========================================
// REGISTRAR SOLICITUD
// ==========================================

$sql_insertar = "INSERT INTO solicitudes
                (
                    id_estudiante,
                    fecha_solicitud,
                    fecha_entrega,
                    estado
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    'PENDIENTE'
                )";

$stmt_insertar = $conexion->prepare($sql_insertar);

$stmt_insertar->bind_param(
    "iss",
    $id_estudiante,
    $fecha_solicitud_mysql,
    $fecha_entrega_mysql
);


// ==========================================
// EJECUTAR INSERT
// ==========================================

if ($stmt_insertar->execute()) {

    $id_solicitud = $stmt_insertar->insert_id;

    $registro_exitoso = true;

} else {

    $registro_exitoso = false;

    $error = $stmt_insertar->error;

}

$stmt_insertar->close();


// ==========================================
// CERRAR CONEXIÓN
// ==========================================

$conexion->close();

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Solicitud de Certificado
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- CSS propio -->

    <link
        rel="stylesheet"
        href="../assets/css/estilos.css"
    >

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <span class="navbar-brand">

            Sistema de Certificados Escolares

        </span>

    </div>

</nav>


<!-- ==========================================
     CONTENIDO
========================================== -->

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-8 col-lg-6">


            <?php if ($registro_exitoso): ?>


                <!-- ==========================================
                     SOLICITUD REGISTRADA
                ========================================== -->

                <div class="card shadow-sm">

                    <div class="card-body p-4 text-center">


                        <div class="alert alert-success">

                            <h3>

                                ¡Solicitud registrada!

                            </h3>

                        </div>


                        <p class="fs-5">

                            Estudiante:

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $estudiante["nombre_completo"]
                                );
                                ?>

                            </strong>

                        </p>


                        <p>

                            Documento:

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $estudiante["documento"]
                                );
                                ?>

                            </strong>

                        </p>


                        <p>

                            Curso:

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $estudiante["curso"]
                                );
                                ?>

                            </strong>

                        </p>


                        <hr>


                        <p>

                            Su solicitud fue registrada
                            correctamente.

                        </p>


                        <div class="alert alert-info">

                            <strong>

                                Fecha de solicitud:

                            </strong>

                            <br>

                            <?php
                            echo $fecha_solicitud->format("d/m/Y H:i");
                            ?>


                            <br><br>


                            <strong>

                                Puede reclamar el certificado
                                a partir del:

                            </strong>

                            <br>

                            <span class="fs-4">

                                <?php
                                echo $fecha_entrega->format("d/m/Y");
                                ?>

                            </span>


                        </div>


                        <p class="text-muted">

                            Recuerde acercarse a Secretaría Académica
                            para reclamar su certificado.

                        </p>


                        <a
                            href="index.php"
                            class="btn btn-primary"
                        >

                            Volver al inicio

                        </a>

                    </div>

                </div>


            <?php else: ?>


                <!-- ==========================================
                     ERROR
                ========================================== -->

                <div class="card shadow-sm">

                    <div class="card-body p-4 text-center">

                        <div class="alert alert-danger">

                            <h4>

                                No fue posible registrar
                                la solicitud.

                            </h4>

                            <p>

                                Por favor, intente nuevamente.

                            </p>

                        </div>


                        <a
                            href="index.php"
                            class="btn btn-primary"
                        >

                            Volver

                        </a>

                    </div>

                </div>


            <?php endif; ?>


        </div>

    </div>

</div>


<!-- ==========================================
     FOOTER
========================================== -->

<footer class="footer mt-4 text-center">

    <p>

        Sistema de Solicitud de Certificados Escolares

    </p>

</footer>


</body>

</html>