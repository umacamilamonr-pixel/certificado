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
// VARIABLES
// ==========================================

$mensaje = "";
$tipo_mensaje = "";


// ==========================================
// PROCESAR CAMBIO DE ESTADO
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_solicitud = intval($_POST["id_solicitud"] ?? 0);
    $nuevo_estado = $_POST["estado"] ?? "";


    // ==========================================
    // VALIDAR DATOS
    // ==========================================

    if ($id_solicitud <= 0) {

        $mensaje = "Solicitud no válida.";
        $tipo_mensaje = "danger";

    } elseif (
        !in_array(
            $nuevo_estado,
            ["ENTREGADO", "RECHAZADO"],
            true
        )
    ) {

        $mensaje = "Estado no válido.";
        $tipo_mensaje = "danger";

    } else {


        // ==========================================
        // ACTUALIZAR SOLICITUD
        // ==========================================

        $sql_actualizar = "UPDATE solicitudes
                           SET estado = ?
                           WHERE id_solicitud = ?
                           AND estado = 'PENDIENTE'";

        $stmt_actualizar = $conexion->prepare(
            $sql_actualizar
        );


        if ($stmt_actualizar) {

            $stmt_actualizar->bind_param(
                "si",
                $nuevo_estado,
                $id_solicitud
            );


            if ($stmt_actualizar->execute()) {

                if ($stmt_actualizar->affected_rows > 0) {

                    if ($nuevo_estado === "ENTREGADO") {

                        $mensaje =
                            "La solicitud fue marcada como entregada.";

                    } else {

                        $mensaje =
                            "La solicitud fue marcada como rechazada.";

                    }

                    $tipo_mensaje = "success";

                } else {

                    $mensaje =
                        "La solicitud no pudo actualizarse o ya no está pendiente.";

                    $tipo_mensaje = "warning";

                }

            } else {

                $mensaje =
                    "Ocurrió un error al actualizar la solicitud.";

                $tipo_mensaje = "danger";

            }


            $stmt_actualizar->close();

        } else {

            $mensaje =
                "No fue posible preparar la actualización.";

            $tipo_mensaje = "danger";

        }

    }

}


// ==========================================
// CONSULTAR SOLICITUDES
// ==========================================

$sql = "SELECT
            s.id_solicitud,
            s.fecha_solicitud,
            s.fecha_entrega,
            s.estado,

            e.id_estudiante,
            e.nombre_completo,
            e.documento,
            e.curso,
            e.jornada

        FROM solicitudes s

        INNER JOIN estudiantes e
            ON s.id_estudiante = e.id_estudiante

        ORDER BY
            CASE
                WHEN s.estado = 'PENDIENTE' THEN 1
                WHEN s.estado = 'RECHAZADO' THEN 2
                WHEN s.estado = 'ENTREGADO' THEN 3
                ELSE 4
            END,

            s.fecha_solicitud DESC";


$resultado = $conexion->query($sql);


// ==========================================
// CERRAR CONEXIÓN
// ==========================================

// No cerramos todavía porque PHP necesita
// recorrer el resultado en la página.

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
        Solicitudes - Secretaría
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

            color: white;

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


        .tabla-solicitudes {

            font-size: 14px;

        }


        .tabla-solicitudes th {

            white-space: nowrap;

        }


        .tabla-solicitudes td {

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
     CONTENEDOR
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
                            class="nav-link active"
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


                    <!-- IMPORTAR -->

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
             CONTENIDO PRINCIPAL
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

                        Solicitudes de certificados

                    </h2>

                    <p class="text-muted mb-0">

                        Consulte y gestione las solicitudes
                        realizadas por los estudiantes.

                    </p>

                </div>


                <a
                    href="dashboard.php"
                    class="btn btn-outline-primary mt-3 mt-md-0"
                >

                    <i class="bi bi-arrow-left"></i>

                    Volver al inicio

                </a>

            </div>


            <!-- ==========================================
                 MENSAJE
            ========================================== -->

            <?php if ($mensaje !== ""): ?>

                <div
                    class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show"
                    role="alert"
                >

                    <?php

                    echo htmlspecialchars($mensaje);

                    ?>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            <?php endif; ?>


            <!-- ==========================================
                 TABLA
            ========================================== -->

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <h5 class="mb-0">

                        <i class="bi bi-list-check"></i>

                        Lista de solicitudes

                    </h5>

                </div>


                <div class="card-body">


                    <?php if ($resultado && $resultado->num_rows > 0): ?>


                        <div class="table-responsive">

                            <table
                                class="table table-hover
                                       table-bordered
                                       tabla-solicitudes
                                       mb-0"
                            >

                                <thead class="table-light">

                                    <tr>

                                        <th>
                                            #
                                        </th>

                                        <th>
                                            Estudiante
                                        </th>

                                        <th>
                                            Documento
                                        </th>

                                        <th>
                                            Curso
                                        </th>

                                        <th>
                                            Jornada
                                        </th>

                                        <th>
                                            Fecha solicitud
                                        </th>

                                        <th>
                                            Fecha entrega
                                        </th>

                                        <th>
                                            Estado
                                        </th>

                                        <th>
                                            Acción
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                <?php while (
                                    $solicitud =
                                    $resultado->fetch_assoc()
                                ): ?>


                                    <tr>


                                        <!-- ID -->

                                        <td>

                                            <?php

                                            echo $solicitud[
                                                "id_solicitud"
                                            ];

                                            ?>

                                        </td>


                                        <!-- NOMBRE -->

                                        <td>

                                            <span
                                                class="nombre-estudiante"
                                            >

                                                <?php

                                                echo htmlspecialchars(
                                                    $solicitud[
                                                        "nombre_completo"
                                                    ]
                                                );

                                                ?>

                                            </span>

                                        </td>


                                        <!-- DOCUMENTO -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $solicitud[
                                                    "documento"
                                                ]
                                            );

                                            ?>

                                        </td>


                                        <!-- CURSO -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $solicitud[
                                                    "curso"
                                                ]
                                            );

                                            ?>

                                        </td>


                                        <!-- JORNADA -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $solicitud[
                                                    "jornada"
                                                ]
                                            );

                                            ?>

                                        </td>


                                        <!-- FECHA SOLICITUD -->

                                        <td>

                                            <?php

                                            if (
                                                !empty(
                                                    $solicitud[
                                                        "fecha_solicitud"
                                                    ]
                                                )
                                            ) {

                                                echo date(
                                                    "d/m/Y H:i",
                                                    strtotime(
                                                        $solicitud[
                                                            "fecha_solicitud"
                                                        ]
                                                    )
                                                );

                                            } else {

                                                echo "-";

                                            }

                                            ?>

                                        </td>


                                        <!-- FECHA ENTREGA -->

                                        <td>

                                            <?php

                                            if (
                                                !empty(
                                                    $solicitud[
                                                        "fecha_entrega"
                                                    ]
                                                )
                                            ) {

                                                echo date(
                                                    "d/m/Y",
                                                    strtotime(
                                                        $solicitud[
                                                            "fecha_entrega"
                                                        ]
                                                    )
                                                );

                                            } else {

                                                echo "-";

                                            }

                                            ?>

                                        </td>


                                        <!-- ESTADO -->

                                        <td>


                                            <?php

                                            $estado =
                                                $solicitud[
                                                    "estado"
                                                ];


                                            if (
                                                $estado ===
                                                "PENDIENTE"
                                            ):

                                            ?>

                                                <span
                                                    class="badge bg-warning text-dark"
                                                >

                                                    <i
                                                        class="bi bi-hourglass-split"
                                                    ></i>

                                                    Pendiente

                                                </span>


                                            <?php

                                            elseif (
                                                $estado ===
                                                "ENTREGADO"
                                            ):

                                            ?>

                                                <span
                                                    class="badge bg-success"
                                                >

                                                    <i
                                                        class="bi bi-check-circle"
                                                    ></i>

                                                    Entregado

                                                </span>


                                            <?php

                                            elseif (
                                                $estado ===
                                                "RECHAZADO"
                                            ):

                                            ?>

                                                <span
                                                    class="badge bg-danger"
                                                >

                                                    <i
                                                        class="bi bi-x-circle"
                                                    ></i>

                                                    Rechazado

                                                </span>


                                            <?php else: ?>

                                                <span
                                                    class="badge bg-secondary"
                                                >

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $estado
                                                    );

                                                    ?>

                                                </span>

                                            <?php endif; ?>


                                        </td>


                                        <!-- ACCIÓN -->

                                        <td>


                                            <?php

                                            if (
                                                $estado ===
                                                "PENDIENTE"
                                            ):

                                            ?>


                                                <div
                                                    class="d-flex
                                                           gap-1"
                                                >


                                                    <!-- ENTREGAR -->

                                                    <form
                                                        method="POST"
                                                        action="solicitudes.php"
                                                        onsubmit="
                                                            return confirm(
                                                                '¿Está seguro de marcar esta solicitud como ENTREGADA?'
                                                            );
                                                        "
                                                    >

                                                        <input
                                                            type="hidden"
                                                            name="id_solicitud"
                                                            value="<?php
                                                                echo $solicitud[
                                                                    "id_solicitud"
                                                                ];
                                                            ?>"
                                                        >


                                                        <input
                                                            type="hidden"
                                                            name="estado"
                                                            value="ENTREGADO"
                                                        >


                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-success"
                                                            title="Marcar como entregado"
                                                        >

                                                            <i
                                                                class="bi bi-check-lg"
                                                            ></i>

                                                            Entregar

                                                        </button>

                                                    </form>


                                                    <!-- RECHAZAR -->

                                                    <form
                                                        method="POST"
                                                        action="solicitudes.php"
                                                        onsubmit="
                                                            return confirm(
                                                                '¿Está seguro de rechazar esta solicitud?'
                                                            );
                                                        "
                                                    >

                                                        <input
                                                            type="hidden"
                                                            name="id_solicitud"
                                                            value="<?php
                                                                echo $solicitud[
                                                                    "id_solicitud"
                                                                ];
                                                            ?>"
                                                        >


                                                        <input
                                                            type="hidden"
                                                            name="estado"
                                                            value="RECHAZADO"
                                                        >


                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            title="Rechazar solicitud"
                                                        >

                                                            <i
                                                                class="bi bi-x-lg"
                                                            ></i>

                                                            Rechazar

                                                        </button>

                                                    </form>


                                                </div>


                                            <?php else: ?>


                                                <span class="text-muted">

                                                    Sin acciones

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
                             NO HAY SOLICITUDES
                        ========================================== -->

                        <div class="text-center py-5">

                            <div class="mb-3">

                                <i
                                    class="bi bi-inbox"
                                    style="font-size: 60px;"
                                ></i>

                            </div>


                            <h4>

                                No hay solicitudes registradas

                            </h4>


                            <p class="text-muted">

                                Cuando un estudiante solicite
                                un certificado, aparecerá aquí.

                            </p>

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
// CERRAR CONEXIÓN
// ==========================================

$conexion->close();

?>