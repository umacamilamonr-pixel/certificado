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
// DATOS DE LA SESIÓN
// ==========================================

$nombre_usuario = $_SESSION["usuario_nombre"];


// ==========================================
// CONTAR ESTUDIANTES
// ==========================================

$sql_estudiantes = "SELECT COUNT(*) AS total
                    FROM estudiantes
                    WHERE estado = 'ACTIVO'";

$resultado_estudiantes = $conexion->query($sql_estudiantes);

$total_estudiantes = 0;

if ($resultado_estudiantes) {

    $fila = $resultado_estudiantes->fetch_assoc();

    $total_estudiantes = $fila["total"];

}


// ==========================================
// CONTAR SOLICITUDES PENDIENTES
// ==========================================

$sql_pendientes = "SELECT COUNT(*) AS total
                   FROM solicitudes
                   WHERE estado = 'PENDIENTE'";

$resultado_pendientes = $conexion->query($sql_pendientes);

$total_pendientes = 0;

if ($resultado_pendientes) {

    $fila = $resultado_pendientes->fetch_assoc();

    $total_pendientes = $fila["total"];

}


// ==========================================
// CONTAR SOLICITUDES ENTREGADAS
// ==========================================

$sql_entregadas = "SELECT COUNT(*) AS total
                   FROM solicitudes
                   WHERE estado = 'ENTREGADO'";

$resultado_entregadas = $conexion->query($sql_entregadas);

$total_entregadas = 0;

if ($resultado_entregadas) {

    $fila = $resultado_entregadas->fetch_assoc();

    $total_entregadas = $fila["total"];

}


// ==========================================
// CONTAR SOLICITUDES RECHAZADAS
// ==========================================

$sql_rechazadas = "SELECT COUNT(*) AS total
                   FROM solicitudes
                   WHERE estado = 'RECHAZADO'";

$resultado_rechazadas = $conexion->query($sql_rechazadas);

$total_rechazadas = 0;

if ($resultado_rechazadas) {

    $fila = $resultado_rechazadas->fetch_assoc();

    $total_rechazadas = $fila["total"];

}


// ==========================================
// TOTAL DE SOLICITUDES
// ==========================================

$sql_total_solicitudes = "SELECT COUNT(*) AS total
                          FROM solicitudes";

$resultado_total = $conexion->query($sql_total_solicitudes);

$total_solicitudes = 0;

if ($resultado_total) {

    $fila = $resultado_total->fetch_assoc();

    $total_solicitudes = $fila["total"];

}


// ==========================================
// CERRAR CONEXIÓN
// ==========================================

$conexion->close();

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
        Panel de Secretaría
    </title>


    <!-- ==========================================
         BOOTSTRAP 5
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
         ESTILOS PROPIOS
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


        .dashboard-card {

            border: none;

            border-radius: 12px;

            transition: 0.2s;

        }


        .dashboard-card:hover {

            transform: translateY(-3px);

        }


        .card-icon {

            font-size: 35px;

        }


        .welcome-card {

            border: none;

            border-radius: 12px;

        }

    </style>

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container-fluid">

        <span class="navbar-brand">

            <i class="bi bi-mortarboard-fill"></i>

            Sistema de Certificados Escolares

        </span>


        <div class="text-white">

            <i class="bi bi-person-circle"></i>

            <?php
            echo htmlspecialchars($nombre_usuario);
            ?>

        </div>

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
                            class="nav-link active"
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
                            class="nav-link"
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

        <main class="col-md-9 col-lg-10 p-4">


            <!-- ==========================================
                 BIENVENIDA
            ========================================== -->

            <div class="card welcome-card shadow-sm mb-4">

                <div class="card-body">

                    <h3>

                        Bienvenida,
                        <?php
                        echo htmlspecialchars($nombre_usuario);
                        ?>

                    </h3>

                    <p class="text-muted mb-0">

                        Desde este panel puede administrar
                        las solicitudes de certificados
                        y los estudiantes.

                    </p>

                </div>

            </div>


            <!-- ==========================================
                 TARJETAS DE ESTADÍSTICAS
            ========================================== -->

            <div class="row g-4">


                <!-- ESTUDIANTES -->

                <div class="col-sm-6 col-xl-3">

                    <div class="card dashboard-card shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">

                                        Estudiantes activos

                                    </p>

                                    <h2>

                                        <?php
                                        echo $total_estudiantes;
                                        ?>

                                    </h2>

                                </div>

                                <div class="card-icon text-primary">

                                    <i class="bi bi-people-fill"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- PENDIENTES -->

                <div class="col-sm-6 col-xl-3">

                    <div class="card dashboard-card shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">

                                        Pendientes

                                    </p>

                                    <h2>

                                        <?php
                                        echo $total_pendientes;
                                        ?>

                                    </h2>

                                </div>

                                <div class="card-icon text-warning">

                                    <i class="bi bi-hourglass-split"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ENTREGADAS -->

                <div class="col-sm-6 col-xl-3">

                    <div class="card dashboard-card shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">

                                        Entregadas

                                    </p>

                                    <h2>

                                        <?php
                                        echo $total_entregadas;
                                        ?>

                                    </h2>

                                </div>

                                <div class="card-icon text-success">

                                    <i class="bi bi-check-circle-fill"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- TOTAL SOLICITUDES -->

                <div class="col-sm-6 col-xl-3">

                    <div class="card dashboard-card shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">

                                        Total solicitudes

                                    </p>

                                    <h2>

                                        <?php
                                        echo $total_solicitudes;
                                        ?>

                                    </h2>

                                </div>

                                <div class="card-icon text-info">

                                    <i class="bi bi-file-earmark-text-fill"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


            </div>


            <!-- ==========================================
                 INFORMACIÓN ADICIONAL
            ========================================== -->

            <div class="row mt-4">


                <!-- ESTADO SOLICITUDES -->

                <div class="col-md-6 mb-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-header">

                            <strong>

                                Estado de las solicitudes

                            </strong>

                        </div>


                        <div class="card-body">


                            <div class="d-flex justify-content-between mb-3">

                                <span>

                                    Pendientes

                                </span>

                                <span class="badge bg-warning text-dark">

                                    <?php
                                    echo $total_pendientes;
                                    ?>

                                </span>

                            </div>


                            <div class="d-flex justify-content-between mb-3">

                                <span>

                                    Entregadas

                                </span>

                                <span class="badge bg-success">

                                    <?php
                                    echo $total_entregadas;
                                    ?>

                                </span>

                            </div>


                            <div class="d-flex justify-content-between">

                                <span>

                                    Rechazadas

                                </span>

                                <span class="badge bg-danger">

                                    <?php
                                    echo $total_rechazadas;
                                    ?>

                                </span>

                            </div>


                        </div>

                    </div>

                </div>


                <!-- ACCIONES RÁPIDAS -->

                <div class="col-md-6 mb-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-header">

                            <strong>

                                Acciones rápidas

                            </strong>

                        </div>


                        <div class="card-body">


                            <div class="d-grid gap-2">


                                <a
                                    href="solicitudes.php"
                                    class="btn btn-primary"
                                >

                                    <i class="bi bi-file-earmark-text"></i>

                                    Ver solicitudes

                                </a>


                                <a
                                    href="estudiantes.php"
                                    class="btn btn-outline-primary"
                                >

                                    <i class="bi bi-people"></i>

                                    Ver estudiantes

                                </a>


                                <a
                                    href="importar.php"
                                    class="btn btn-outline-success"
                                >

                                    <i class="bi bi-file-earmark-excel"></i>

                                    Importar estudiantes desde Excel

                                </a>


                            </div>


                        </div>

                    </div>

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
        &copy; <?php echo date("Y"); ?>

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
