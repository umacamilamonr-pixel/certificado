<?php

// ==========================================
// INICIO DE SESIÓN
// ==========================================

session_start();


// ==========================================
// CONEXIÓN A LA BASE DE DATOS
// ==========================================

require_once "../config/conexion.php";


// ==========================================
// VARIABLES
// ==========================================

$estudiante = null;
$solicitud_pendiente = false;
$mensaje = "";
$tipo_mensaje = "";


// ==========================================
// CONSULTAR ESTUDIANTE
// ==========================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtener documento
    $documento = trim($_POST["documento"] ?? "");


    // Validar que no esté vacío
    if ($documento == "") {

        $mensaje = "Por favor, ingrese su número de documento.";
        $tipo_mensaje = "warning";

    } else {

        // ==========================================
        // BUSCAR ESTUDIANTE
        // ==========================================

        $sql = "SELECT 
                    id_estudiante,
                    tipo_documento,
                    documento,
                    nombre_completo,
                    curso,
                    jornada,
                    estado
                FROM estudiantes
                WHERE documento = ?
                LIMIT 1";

        $stmt = $conexion->prepare($sql);

        $stmt->bind_param("s", $documento);

        $stmt->execute();

        $resultado = $stmt->get_result();


        // ==========================================
        // VERIFICAR SI EXISTE
        // ==========================================

        if ($resultado->num_rows > 0) {

            $estudiante = $resultado->fetch_assoc();


            // ==========================================
            // VERIFICAR ESTADO DEL ESTUDIANTE
            // ==========================================

            if ($estudiante["estado"] != "ACTIVO") {

                $mensaje = "El estudiante se encuentra registrado, pero actualmente no está activo.";
                $tipo_mensaje = "warning";

                $estudiante = null;

            } else {

                // ==========================================
                // BUSCAR SOLICITUD PENDIENTE
                // ==========================================

                $sql_solicitud = "SELECT id_solicitud
                                  FROM solicitudes
                                  WHERE id_estudiante = ?
                                  AND estado = 'PENDIENTE'
                                  LIMIT 1";

                $stmt_solicitud = $conexion->prepare($sql_solicitud);

                $stmt_solicitud->bind_param(
                    "i",
                    $estudiante["id_estudiante"]
                );

                $stmt_solicitud->execute();

                $resultado_solicitud = $stmt_solicitud->get_result();


                if ($resultado_solicitud->num_rows > 0) {

                    $solicitud_pendiente = true;

                }

                $stmt_solicitud->close();
            }

        } else {

            $mensaje = "No se encontró un estudiante registrado con ese número de documento.";
            $tipo_mensaje = "danger";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Solicitud de Certificados</title>


    <!-- Bootstrap 5 -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Estilos propios -->

    <link
        rel="stylesheet"
        href="../assets/css/estilos.css"
    >

</head>


<body>


<!-- ==========================================
     BARRA SUPERIOR
========================================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <span class="navbar-brand mb-0 h1">

            Sistema de Certificados Escolares

        </span>

    </div>

</nav>


<!-- ==========================================
     CONTENIDO PRINCIPAL
========================================== -->

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-8 col-lg-6">


            <!-- ==========================================
                 TARJETA PRINCIPAL
            ========================================== -->

            <div class="card shadow-sm">


                <!-- ENCABEZADO -->

                <div class="card-header bg-primary text-white text-center">

                    <h4 class="mb-0">

                        Solicitud de Certificado de Estudio

                    </h4>

                </div>


                <!-- CUERPO -->

                <div class="card-body p-4">


                    <!-- ==========================================
                         MENSAJE DEL SISTEMA
                    ========================================== -->

                    <?php if ($mensaje != ""): ?>

                        <div
                            class="alert alert-<?php echo $tipo_mensaje; ?>"
                            role="alert"
                        >

                            <?php echo htmlspecialchars($mensaje); ?>

                        </div>

                    <?php endif; ?>


                    <!-- ==========================================
                         FORMULARIO DE CONSULTA
                    ========================================== -->

                    <form method="POST" action="index.php">


                        <div class="mb-3">

                            <label
                                for="documento"
                                class="form-label"
                            >

                                Número de documento

                            </label>


                            <input
                                type="text"
                                class="form-control form-control-lg"
                                id="documento"
                                name="documento"
                                placeholder="Ingrese su número de documento"
                                maxlength="20"
                                required
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST["documento"] ?? ""
                                    );
                                ?>"
                            >

                            <div class="form-text">

                                Ingrese el número de documento registrado
                                en la institución.

                            </div>

                        </div>


                        <div class="d-grid">

                            <button
                                type="submit"
                                class="btn btn-primary btn-lg"
                            >

                                Consultar

                            </button>

                        </div>

                    </form>


                    <!-- ==========================================
                         INFORMACIÓN DEL ESTUDIANTE
                    ========================================== -->

                    <?php if ($estudiante != null): ?>

                        <hr class="my-4">


                        <h5 class="mb-3">

                            Información del estudiante

                        </h5>


                        <!-- NOMBRE -->

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Nombre completo

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?php
                                    echo htmlspecialchars(
                                        $estudiante["nombre_completo"]
                                    );
                                ?>"
                                readonly
                            >

                        </div>


                        <!-- DOCUMENTO -->

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Número de documento

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?php
                                    echo htmlspecialchars(
                                        $estudiante["documento"]
                                    );
                                ?>"
                                readonly
                            >

                        </div>


                        <!-- CURSO -->

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Curso

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?php
                                    echo htmlspecialchars(
                                        $estudiante["curso"]
                                    );
                                ?>"
                                readonly
                            >

                        </div>


                        <!-- JORNADA -->

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Jornada

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?php
                                    echo htmlspecialchars(
                                        $estudiante["jornada"]
                                    );
                                ?>"
                                readonly
                            >

                        </div>


                        <!-- ==========================================
                             SOLICITUD PENDIENTE
                        ========================================== -->

                        <?php if ($solicitud_pendiente): ?>

                            <div class="alert alert-warning text-center mt-4">

                                <h5 class="alert-heading">

                                    Certificado en trámite

                                </h5>

                                <p class="mb-0">

                                    Ya existe una solicitud de certificado
                                    en trámite.

                                    Podrá reclamarlo en Secretaría
                                    dentro de los próximos

                                    <strong>3 días hábiles</strong>.

                                </p>

                            </div>


                        <?php else: ?>


                            <!-- ==========================================
                                 BOTÓN SOLICITAR
                            ========================================== -->

                            <div class="alert alert-success text-center mt-4">

                                <p class="mb-3">

                                    Sus datos fueron encontrados
                                    correctamente.

                                </p>


                                <form
                                    method="POST"
                                    action="solicitar.php"
                                >

                                    <input
                                        type="hidden"
                                        name="id_estudiante"
                                        value="<?php
                                            echo $estudiante["id_estudiante"];
                                        ?>"
                                    >


                                    <button
                                        type="submit"
                                        class="btn btn-success btn-lg"
                                    >

                                        Solicitar certificado

                                    </button>

                                </form>

                            </div>

                        <?php endif; ?>


                    <?php endif; ?>


                </div>

            </div>


            <!-- ==========================================
                 ACCESO SECRETARÍA
            ========================================== -->

            <div class="text-center mt-4">

                <a
                    href="../secretaria/login.php"
                    class="btn btn-outline-secondary"
                >

                    Acceso Secretaría Académica

                </a>

            </div>


        </div>

    </div>

</div>


<!-- ==========================================
     FOOTER
========================================== -->

<footer class="footer mt-4">

    <p>

        Sistema de Solicitud de Certificados Escolares

    </p>

    <p>

        &copy; <?php echo date("Y"); ?>

        Institución Educativa

    </p>

</footer>


<!-- Bootstrap JavaScript -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>