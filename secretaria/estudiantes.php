<?php

// ==========================================
// INICIAR SESIÓN
// ==========================================

session_start();


// ==========================================
// VERIFICAR ACCESO
// ==========================================

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "SECRETARIA"
) {

    header("Location: login.php");
    exit();

}


// ==========================================
// CONEXIÓN A LA BASE DE DATOS
// ==========================================

require_once "../config/conexion.php";


// ==========================================
// OBTENER TÉRMINO DE BÚSQUEDA
// ==========================================

$busqueda = trim($_GET["buscar"] ?? "");


// ==========================================
// CONSULTAR ESTUDIANTES
// ==========================================

if ($busqueda !== "") {

    $sql = "SELECT
                id_estudiante,
                documento,
                nombre_completo,
                curso,
                jornada,
                estado
            FROM estudiantes
            WHERE documento LIKE ?
               OR nombre_completo LIKE ?
               OR curso LIKE ?
            ORDER BY nombre_completo ASC";

    $stmt = $conexion->prepare($sql);

    $termino = "%" . $busqueda . "%";

    $stmt->bind_param(
        "sss",
        $termino,
        $termino,
        $termino
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

} else {

    $sql = "SELECT
                id_estudiante,
                documento,
                nombre_completo,
                curso,
                jornada,
                estado
            FROM estudiantes
            ORDER BY nombre_completo ASC";

    $resultado = $conexion->query($sql);

}


// ==========================================
// CONTAR ESTUDIANTES
// ==========================================

$sql_total = "SELECT COUNT(*) AS total
              FROM estudiantes";

$resultado_total = $conexion->query($sql_total);

$total_estudiantes = 0;

if ($resultado_total) {

    $fila_total = $resultado_total->fetch_assoc();

    $total_estudiantes = $fila_total["total"];

}


// ==========================================
// CONTAR ESTUDIANTES ACTIVOS
// ==========================================

$sql_activos = "SELECT COUNT(*) AS total
                FROM estudiantes
                WHERE estado = 'ACTIVO'";

$resultado_activos = $conexion->query($sql_activos);

$total_activos = 0;

if ($resultado_activos) {

    $fila_activos = $resultado_activos->fetch_assoc();

    $total_activos = $fila_activos["total"];

}


// ==========================================
// CONTAR ESTUDIANTES INACTIVOS
// ==========================================

$sql_inactivos = "SELECT COUNT(*) AS total
                  FROM estudiantes
                  WHERE estado <> 'ACTIVO'";

$resultado_inactivos = $conexion->query($sql_inactivos);

$total_inactivos = 0;

if ($resultado_inactivos) {

    $fila_inactivos = $resultado_inactivos->fetch_assoc();

    $total_inactivos = $fila_inactivos["total"];

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Estudiantes - Secretaría
    </title>


    <!-- ==========================================
         BOOTSTRAP
    ========================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- ==========================================
         BOOTSTRAP ICONS
    ========================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- ==========================================
         ESTILOS
    ========================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/estilos.css"
    >


    <style>

        body {

            background-color: #f4f6f9;

        }


        .sidebar {

            min-height: calc(100vh - 56px);

            background-color: #212529;

        }


        .sidebar .nav-link {

            color: #ffffff;

            padding: 12px 18px;

        }


        .sidebar .nav-link:hover {

            background-color: #343a40;

        }


        .sidebar .nav-link.active {

            background-color: #0d6efd;

        }


        .contenido {

            min-height: calc(100vh - 56px);

        }


        .estadistica {

            border: none;

            border-radius: 12px;

        }


        .tabla-estudiantes {

            font-size: 14px;

        }


        .tabla-estudiantes th {

            white-space: nowrap;

        }


        .tabla-estudiantes td {

            vertical-align: middle;

        }


        .nombre-estudiante {

            font-weight: 600;

        }

    </style>

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container-fluid">

        <a
            href="dashboard.php"
            class="navbar-brand"
        >

            <i class="bi bi-mortarboard-fill"></i>

            Sistema de Certificados Escolares

        </a>


        <span class="text-white">

            <i class="bi bi-person-circle"></i>

            <?php

            echo htmlspecialchars(
                $_SESSION["usuario_nombre"]
            );

            ?>

        </span>

    </div>

</nav>


<!-- ==========================================
     CONTENEDOR PRINCIPAL
========================================== -->

<div class="container-fluid">

    <div class="row">


        <!-- ==========================================
             MENÚ LATERAL
        ========================================== -->

        <aside class="col-md-3 col-lg-2 p-0 sidebar">

            <div class="p-3">

                <h6 class="text-white text-center mb-3">

                    MENÚ PRINCIPAL

                </h6>


                <ul class="nav nav-pills flex-column">


                    <!-- INICIO -->

                    <li class="nav-item">

                        <a
                            href="dashboard.php"
                            class="nav-link"
                        >

                            <i class="bi bi-house-door"></i>

                            Inicio

                        </a>

                    </li>


                    <!-- SOLICITUDES -->

                    <li class="nav-item">

                        <a
                            href="solicitudes.php"
                            class="nav-link"
                        >

                            <i class="bi bi-file-earmark-text"></i>

                            Solicitudes

                        </a>

                    </li>


                    <!-- ESTUDIANTES -->

                    <li class="nav-item">

                        <a
                            href="estudiantes.php"
                            class="nav-link active"
                        >

                            <i class="bi bi-people"></i>

                            Estudiantes

                        </a>

                    </li>


                    <!-- IMPORTAR EXCEL -->

                    <li class="nav-item">

                        <a
                            href="importar.php"
                            class="nav-link"
                        >

                            <i class="bi bi-file-earmark-excel"></i>

                            Importar Excel

                        </a>

                    </li>


                    <hr class="text-secondary">


                    <!-- CERRAR SESIÓN -->

                    <li class="nav-item">

                        <a
                            href="logout.php"
                            class="nav-link text-danger"
                        >

                            <i class="bi bi-box-arrow-right"></i>

                            Cerrar sesión

                        </a>

                    </li>

                </ul>

            </div>

        </aside>


        <!-- ==========================================
             CONTENIDO
        ========================================== -->

        <main class="col-md-9 col-lg-10 p-4 contenido">


            <!-- ==========================================
                 ENCABEZADO
            ========================================== -->

            <div
                class="d-flex flex-column flex-md-row
                       justify-content-between
                       align-items-md-center
                       mb-4"
            >

                <div>

                    <h2>

                        Estudiantes

                    </h2>

                    <p class="text-muted mb-0">

                        Consulte los estudiantes registrados
                        en el sistema.

                    </p>

                </div>


                <a
                    href="importar.php"
                    class="btn btn-success mt-3 mt-md-0"
                >

                    <i class="bi bi-file-earmark-excel"></i>

                    Importar Excel

                </a>

            </div>


            <!-- ==========================================
                 TARJETAS DE ESTADÍSTICAS
            ========================================== -->

            <div class="row g-3 mb-4">


                <!-- TOTAL -->

                <div class="col-md-4">

                    <div class="card estadistica shadow-sm">

                        <div class="card-body">

                            <div class="d-flex
                                        justify-content-between
                                        align-items-center">

                                <div>

                                    <p class="text-muted mb-1">

                                        Total estudiantes

                                    </p>

                                    <h3 class="mb-0">

                                        <?php
                                        echo $total_estudiantes;
                                        ?>

                                    </h3>

                                </div>


                                <i
                                    class="bi bi-people-fill
                                           text-primary"
                                    style="font-size: 35px;"
                                ></i>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ACTIVOS -->

                <div class="col-md-4">

                    <div class="card estadistica shadow-sm">

                        <div class="card-body">

                            <div class="d-flex
                                        justify-content-between
                                        align-items-center">

                                <div>

                                    <p class="text-muted mb-1">

                                        Estudiantes activos

                                    </p>

                                    <h3 class="mb-0">

                                        <?php
                                        echo $total_activos;
                                        ?>

                                    </h3>

                                </div>


                                <i
                                    class="bi bi-person-check-fill
                                           text-success"
                                    style="font-size: 35px;"
                                ></i>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- INACTIVOS -->

                <div class="col-md-4">

                    <div class="card estadistica shadow-sm">

                        <div class="card-body">

                            <div class="d-flex
                                        justify-content-between
                                        align-items-center">

                                <div>

                                    <p class="text-muted mb-1">

                                        Estudiantes inactivos

                                    </p>

                                    <h3 class="mb-0">

                                        <?php
                                        echo $total_inactivos;
                                        ?>

                                    </h3>

                                </div>


                                <i
                                    class="bi bi-person-x-fill
                                           text-danger"
                                    style="font-size: 35px;"
                                ></i>

                            </div>

                        </div>

                    </div>

                </div>


            </div>


            <!-- ==========================================
                 BUSCADOR
            ========================================== -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-body">


                    <form
                        method="GET"
                        action="estudiantes.php"
                    >

                        <div class="row g-2 align-items-end">


                            <div class="col-md-9">

                                <label
                                    for="buscar"
                                    class="form-label fw-semibold"
                                >

                                    Buscar estudiante

                                </label>


                                <input
                                    type="text"
                                    class="form-control"
                                    id="buscar"
                                    name="buscar"
                                    placeholder="Documento, nombre o curso..."
                                    value="<?php
                                        echo htmlspecialchars(
                                            $busqueda
                                        );
                                    ?>"
                                >

                            </div>


                            <div class="col-md-3">

                                <div class="d-grid gap-2">


                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >

                                        <i
                                            class="bi bi-search"
                                        ></i>

                                        Buscar

                                    </button>


                                </div>

                            </div>

                        </div>


                    </form>


                    <?php if ($busqueda !== ""): ?>

                        <div class="mt-3">

                            <span class="text-muted">

                                Resultados para:

                            </span>

                            <strong>

                                "<?php
                                echo htmlspecialchars(
                                    $busqueda
                                );
                                ?>"

                            </strong>


                            <a
                                href="estudiantes.php"
                                class="btn btn-sm btn-outline-secondary ms-2"
                            >

                                Limpiar búsqueda

                            </a>

                        </div>

                    <?php endif; ?>


                </div>

            </div>


            <!-- ==========================================
                 TABLA DE ESTUDIANTES
            ========================================== -->

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <h5 class="mb-0">

                        <i class="bi bi-list-ul"></i>

                        Estudiantes registrados

                    </h5>

                </div>


                <div class="card-body">


                    <?php if (
                        $resultado &&
                        $resultado->num_rows > 0
                    ): ?>


                        <div class="table-responsive">

                            <table
                                class="table table-hover
                                       table-bordered
                                       tabla-estudiantes
                                       mb-0"
                            >

                                <thead class="table-light">

                                    <tr>

                                        <th>
                                            #
                                        </th>

                                        <th>
                                            Documento
                                        </th>

                                        <th>
                                            Nombre completo
                                        </th>

                                        <th>
                                            Curso
                                        </th>

                                        <th>
                                            Jornada
                                        </th>

                                        <th>
                                            Estado
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                <?php while (
                                    $estudiante =
                                    $resultado->fetch_assoc()
                                ): ?>


                                    <tr>


                                        <!-- ID -->

                                        <td>

                                            <?php

                                            echo $estudiante[
                                                "id_estudiante"
                                            ];

                                            ?>

                                        </td>


                                        <!-- DOCUMENTO -->

                                        <td>

                                            <strong>

                                                <?php

                                                echo htmlspecialchars(
                                                    $estudiante[
                                                        "documento"
                                                    ]
                                                );

                                                ?>

                                            </strong>

                                        </td>


                                        <!-- NOMBRE -->

                                        <td>

                                            <span
                                                class="nombre-estudiante"
                                            >

                                                <?php

                                                echo htmlspecialchars(
                                                    $estudiante[
                                                        "nombre_completo"
                                                    ]
                                                );

                                                ?>

                                            </span>

                                        </td>


                                        <!-- CURSO -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $estudiante[
                                                    "curso"
                                                ]
                                            );

                                            ?>

                                        </td>


                                        <!-- JORNADA -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $estudiante[
                                                    "jornada"
                                                ]
                                            );

                                            ?>

                                        </td>


                                        <!-- ESTADO -->

                                        <td>


                                            <?php

                                            if (
                                                $estudiante[
                                                    "estado"
                                                ] === "ACTIVO"
                                            ):

                                            ?>

                                                <span
                                                    class="badge bg-success"
                                                >

                                                    <i
                                                        class="bi bi-check-circle"
                                                    ></i>

                                                    Activo

                                                </span>


                                            <?php else: ?>

                                                <span
                                                    class="badge bg-danger"
                                                >

                                                    <i
                                                        class="bi bi-x-circle"
                                                    ></i>

                                                    Inactivo

                                                </span>

                                            <?php endif; ?>


                                        </td>


                                    </tr>


                                <?php endwhile; ?>


                                </tbody>

                            </table>

                        </div>


                    <?php else: ?>


                        <!-- ==========================================
                             SIN RESULTADOS
                        ========================================== -->

                        <div class="text-center py-5">


                            <i
                                class="bi bi-person-x"
                                style="font-size: 60px;"
                            ></i>


                            <h4 class="mt-3">

                                No se encontraron estudiantes

                            </h4>


                            <?php if ($busqueda !== ""): ?>

                                <p class="text-muted">

                                    No existen estudiantes que
                                    coincidan con la búsqueda.

                                </p>

                            <?php else: ?>

                                <p class="text-muted">

                                    Todavía no hay estudiantes
                                    registrados en el sistema.

                                </p>

                            <?php endif; ?>


                        </div>


                    <?php endif; ?>


                </div>

            </div>


        </main>

    </div>

</div>


<!-- ==========================================
     FOOTER
========================================== -->

<footer class="bg-white border-top text-center py-3">

    <small class="text-muted">

        Sistema de Solicitud de Certificados Escolares

        &copy;

        <?php echo date("Y"); ?>

    </small>

</footer>


<!-- ==========================================
     BOOTSTRAP JS
========================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>

<?php

// ==========================================
// CERRAR RECURSOS
// ==========================================

if (isset($stmt)) {

    $stmt->close();

}

$conexion->close();

?>