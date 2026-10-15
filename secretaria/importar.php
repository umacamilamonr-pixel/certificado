<?php

session_start();

// =====================================================
// VERIFICAR SESIÓN DE SECRETARIA
// =====================================================

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "SECRETARIA") {
    header("Location: login.php");
    exit;
}


// =====================================================
// CONEXIÓN A LA BASE DE DATOS
// =====================================================

require_once "../config/conexion.php";


// =====================================================
// PHPSPREADSHEET
// =====================================================

require_once "../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


// =====================================================
// VARIABLES
// =====================================================

$mensaje = "";
$tipoMensaje = "";

$importados = 0;
$omitidos = 0;
$errores = [];


// =====================================================
// VALORES PERMITIDOS
// =====================================================

$estadosPermitidos = [
    "ACTIVO",
    "RETIRADO"
];


// =====================================================
// DESCARGAR PLANTILLA EXCEL
// =====================================================

if (isset($_GET["descargar"]) && $_GET["descargar"] == "1") {

    $spreadsheet = new Spreadsheet();

    $hoja = $spreadsheet->getActiveSheet();

    $hoja->setTitle("Estudiantes");


    // -------------------------------------------------
    // ENCABEZADOS
    // -------------------------------------------------

    $encabezados = [
        "Tipo documento",
        "Documento",
        "Nombre Completo",
        "Curso",
        "Sede",
        "Jornada",
        "Estado"
    ];

    $hoja->fromArray(
        $encabezados,
        null,
        "A1"
    );


    // -------------------------------------------------
    // EJEMPLO
    // -------------------------------------------------

    $ejemplo = [
        "CC",
        "1001234567",
        "Juan Pérez",
        "1103",
        "SEDE PRINCIPAL",
        "MAÑANA",
        "ACTIVO"
    ];

    $hoja->fromArray(
        $ejemplo,
        null,
        "A2"
    );


    // -------------------------------------------------
    // AJUSTAR ANCHO DE COLUMNAS
    // -------------------------------------------------

    foreach (range("A", "G") as $columna) {
        $hoja->getColumnDimension($columna)->setAutoSize(true);
    }


    // -------------------------------------------------
    // CONGELAR ENCABEZADOS
    // -------------------------------------------------

    $hoja->freezePane("A2");


    // -------------------------------------------------
    // DESCARGA
    // -------------------------------------------------

    $nombreArchivo = "plantilla_estudiantes.xlsx";

    header(
        "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    );

    header(
        "Content-Disposition: attachment; filename=\"" . $nombreArchivo . "\""
    );

    header("Cache-Control: max-age=0");

    $writer = new Xlsx($spreadsheet);

    $writer->save("php://output");

    exit;
}


// =====================================================
// PROCESAR IMPORTACIÓN
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // -------------------------------------------------
    // VERIFICAR ARCHIVO
    // -------------------------------------------------

    if (!isset($_FILES["archivo_excel"])) {

        $mensaje = "No se seleccionó ningún archivo.";
        $tipoMensaje = "danger";

    } else {

        $archivo = $_FILES["archivo_excel"];


        // -------------------------------------------------
        // VERIFICAR ERROR DE SUBIDA
        // -------------------------------------------------

        if ($archivo["error"] !== UPLOAD_ERR_OK) {

            $mensaje = "Ocurrió un error al subir el archivo.";
            $tipoMensaje = "danger";

        } else {

            // -------------------------------------------------
            // VERIFICAR EXTENSIÓN
            // -------------------------------------------------

            $extension = strtolower(
                pathinfo($archivo["name"], PATHINFO_EXTENSION)
            );

            if ($extension !== "xlsx") {

                $mensaje = "El archivo debe estar en formato .xlsx";
                $tipoMensaje = "danger";

            } 
            
            // -------------------------------------------------
            // VERIFICAR TAMAÑO
            // -------------------------------------------------

            elseif ($archivo["size"] > 5 * 1024 * 1024) {

                $mensaje = "El archivo no puede superar los 5 MB.";
                $tipoMensaje = "danger";

            } 
            
            else {

                try {

                    // -------------------------------------------------
                    // LEER EXCEL
                    // -------------------------------------------------

                    $spreadsheet = IOFactory::load(
                        $archivo["tmp_name"]
                    );

                    $hoja = $spreadsheet->getActiveSheet();

                    $filas = $hoja->toArray(
                        null,
                        true,
                        true,
                        true
                    );


                    // -------------------------------------------------
                    // VERIFICAR QUE EXISTAN FILAS
                    // -------------------------------------------------

                    if (count($filas) < 2) {

                        throw new Exception(
                            "El archivo Excel no contiene estudiantes para importar."
                        );
                    }


                    // -------------------------------------------------
                    // LEER ENCABEZADOS
                    // -------------------------------------------------

                    $encabezadosExcel = $filas[1];

                    $encabezadosEsperados = [
                        "Tipo documento",
                        "Documento",
                        "Nombre Completo",
                        "Curso",
                        "Sede",
                        "Jornada",
                        "Estado"
                    ];


                    // -------------------------------------------------
                    // VERIFICAR ENCABEZADOS
                    // -------------------------------------------------

                    $encabezadosCorrectos = true;

                    foreach ($encabezadosEsperados as $indice => $encabezado) {

                        $columna = chr(65 + $indice);

                        $valorExcel = isset(
                            $encabezadosExcel[$columna]
                        )
                            ? trim(
                                (string)$encabezadosExcel[$columna]
                            )
                            : "";

                        if (
                            strtolower($valorExcel) !==
                            strtolower($encabezado)
                        ) {

                            $encabezadosCorrectos = false;

                            break;
                        }
                    }


                    if (!$encabezadosCorrectos) {

                        throw new Exception(
                            "Los encabezados del archivo no son correctos. Descarga la plantilla y utiliza sus columnas."
                        );
                    }


                    // -------------------------------------------------
                    // RECORRER ESTUDIANTES
                    // -------------------------------------------------

                    foreach ($filas as $numeroFila => $fila) {

                        // Saltar encabezado
                        if ($numeroFila == 1) {
                            continue;
                        }


                        // -------------------------------------------------
                        // OBTENER DATOS
                        // -------------------------------------------------

                        $tipoDocumento = trim(
                            (string)($fila["A"] ?? "")
                        );

                        $documento = trim(
                            (string)($fila["B"] ?? "")
                        );

                        $nombreCompleto = trim(
                            (string)($fila["C"] ?? "")
                        );

                        $curso = trim(
                            (string)($fila["D"] ?? "")
                        );

                        $sede = trim(
                            (string)($fila["E"] ?? "")
                        );

                        $jornada = strtoupper(
                            trim(
                                (string)($fila["F"] ?? "")
                            )
                        );

                        $estado = strtoupper(
                            trim(
                                (string)($fila["G"] ?? "")
                            )
                        );


                        // -------------------------------------------------
                        // VERIFICAR FILA VACÍA
                        // -------------------------------------------------

                        if (
                            $tipoDocumento === "" &&
                            $documento === "" &&
                            $nombreCompleto === "" &&
                            $curso === "" &&
                            $sede === "" &&
                            $jornada === "" &&
                            $estado === ""
                        ) {

                            continue;
                        }


                        // -------------------------------------------------
                        // CAMPOS OBLIGATORIOS
                        // -------------------------------------------------

                        if (
                            $tipoDocumento === "" ||
                            $documento === "" ||
                            $nombreCompleto === "" ||
                            $curso === "" ||
                            $sede === "" ||
                            $jornada === "" ||
                            $estado === ""
                        ) {

                            $errores[] =
                                "Fila {$numeroFila}: todos los campos son obligatorios.";

                            $omitidos++;

                            continue;
                        }


                        // -------------------------------------------------
                        // VALIDAR ESTADO
                        // -------------------------------------------------

                        if (!in_array(
                            $estado,
                            $estadosPermitidos,
                            true
                        )) {

                            $errores[] =
                                "Fila {$numeroFila}: estado '{$estado}' no válido. Debe ser ACTIVO o RETIRADO.";

                            $omitidos++;

                            continue;
                        }


                        // -------------------------------------------------
                        // VERIFICAR DOCUMENTO REPETIDO
                        // -------------------------------------------------

                        $sqlBuscar = "
                            SELECT id_estudiante
                            FROM estudiantes
                            WHERE documento = ?
                            LIMIT 1
                        ";

                        $stmtBuscar = $conexion->prepare(
                            $sqlBuscar
                        );

                        if (!$stmtBuscar) {

                            $errores[] =
                                "Fila {$numeroFila}: error al preparar la consulta.";

                            $omitidos++;

                            continue;
                        }

                        $stmtBuscar->bind_param(
                            "s",
                            $documento
                        );

                        $stmtBuscar->execute();

                        $resultado = $stmtBuscar->get_result();


                        if ($resultado->num_rows > 0) {

                            $errores[] =
                                "Fila {$numeroFila}: el documento {$documento} ya existe en la base de datos.";

                            $omitidos++;

                            $stmtBuscar->close();

                            continue;
                        }

                        $stmtBuscar->close();


                        // -------------------------------------------------
                        // INSERTAR ESTUDIANTE
                        // -------------------------------------------------

                        $sqlInsertar = "
                            INSERT INTO estudiantes (
                                tipo_documento,
                                documento,
                                nombre_completo,
                                curso,
                                sede,
                                jornada,
                                estado
                            )
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                        ";

                        $stmtInsertar = $conexion->prepare(
                            $sqlInsertar
                        );


                        if (!$stmtInsertar) {

                            $errores[] =
                                "Fila {$numeroFila}: no se pudo preparar la inserción.";

                            $omitidos++;

                            continue;
                        }


                        $stmtInsertar->bind_param(
                            "sssssss",
                            $tipoDocumento,
                            $documento,
                            $nombreCompleto,
                            $curso,
                            $sede,
                            $jornada,
                            $estado
                        );


                        if ($stmtInsertar->execute()) {

                            $importados++;

                        } else {

                            $errores[] =
                                "Fila {$numeroFila}: error al insertar el estudiante.";

                            $omitidos++;
                        }


                        $stmtInsertar->close();
                    }


                    // -------------------------------------------------
                    // MENSAJE FINAL
                    // -------------------------------------------------

                    if ($importados > 0 && $omitidos == 0) {

                        $mensaje =
                            "Importación completada correctamente. Se importaron {$importados} estudiantes.";

                        $tipoMensaje = "success";

                    } elseif ($importados > 0 && $omitidos > 0) {

                        $mensaje =
                            "Importación terminada. Se importaron {$importados} estudiantes y se omitieron {$omitidos}.";

                        $tipoMensaje = "warning";

                    } else {

                        $mensaje =
                            "No se importaron estudiantes. Revisa los errores.";

                        $tipoMensaje = "danger";
                    }

                } catch (Exception $e) {

                    $mensaje =
                        "Error: " . $e->getMessage();

                    $tipoMensaje = "danger";
                }
            }
        }
    }
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

    <title>Importar estudiantes</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        body {
            background-color: #f5f7fb;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }

        .sidebar .titulo {
            color: white;
            font-size: 20px;
            font-weight: bold;
            padding: 25px 20px;
            border-bottom: 1px solid #444;
        }

        .sidebar a {
            display: block;
            color: #ddd;
            text-decoration: none;
            padding: 14px 20px;
        }

        .sidebar a:hover,
        .sidebar a.activo {
            background-color: #343a40;
            color: white;
        }

        .contenido {
            padding: 30px;
        }

        .tarjeta {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .icono {
            font-size: 45px;
        }

        .errores {
            max-height: 350px;
            overflow-y: auto;
        }

    </style>

</head>


<body>


<div class="container-fluid">

    <div class="row">


        <!-- =====================================================
             SIDEBAR
        ====================================================== -->

        <div class="col-md-3 col-lg-2 p-0 sidebar">

            <div class="titulo">

                <i class="bi bi-mortarboard-fill"></i>

                Certificados

            </div>


            <a href="dashboard.php">

                <i class="bi bi-speedometer2"></i>

                Dashboard

            </a>


            <a href="solicitudes.php">

                <i class="bi bi-file-earmark-text"></i>

                Solicitudes

            </a>


            <a href="estudiantes.php">

                <i class="bi bi-people"></i>

                Estudiantes

            </a>


            <a
                href="importar.php"
                class="activo"
            >

                <i class="bi bi-file-earmark-excel"></i>

                Importar Excel

            </a>


            <a href="logout.php">

                <i class="bi bi-box-arrow-right"></i>

                Cerrar sesión

            </a>

        </div>


        <!-- =====================================================
             CONTENIDO
        ====================================================== -->

        <div class="col-md-9 col-lg-10 contenido">


            <!-- TÍTULO -->

            <div class="mb-4">

                <h2>

                    <i class="bi bi-file-earmark-excel"></i>

                    Importar estudiantes

                </h2>

                <p class="text-muted">

                    Carga varios estudiantes utilizando un archivo Excel.

                </p>

            </div>



            <!-- =================================================
                 MENSAJE
            ================================================== -->

            <?php if ($mensaje !== ""): ?>

                <div
                    class="alert alert-<?php echo $tipoMensaje; ?>"
                    role="alert"
                >

                    <i class="bi bi-info-circle"></i>

                    <?php echo htmlspecialchars($mensaje); ?>

                </div>

            <?php endif; ?>



            <!-- =================================================
                 DESCARGAR PLANTILLA
            ================================================== -->

            <div class="card tarjeta mb-4">

                <div class="card-body p-4">

                    <div class="row align-items-center">

                        <div class="col-md-8">

                            <h4>

                                <i class="bi bi-download"></i>

                                1. Descargar plantilla

                            </h4>

                            <p class="text-muted mb-2">

                                Descarga la plantilla oficial para evitar errores
                                al importar los estudiantes.

                            </p>

                            <p class="mb-0">

                               
                                <strong>Estado permitido:</strong>

                                ACTIVO, RETIRADO

                            </p>

                        </div>


                        <div class="col-md-4 text-md-end mt-3 mt-md-0">

                            <a
                                href="importar.php?descargar=1"
                                class="btn btn-success btn-lg"
                            >

                                <i class="bi bi-file-earmark-excel"></i>

                                Descargar plantilla

                            </a>

                        </div>

                    </div>

                </div>

            </div>



            <!-- =================================================
                 SUBIR ARCHIVO
            ================================================== -->

            <div class="card tarjeta mb-4">

                <div class="card-body p-4">

                    <h4>

                        <i class="bi bi-upload"></i>

                        2. Subir archivo Excel

                    </h4>

                    <p class="text-muted">

                        Selecciona el archivo Excel que contiene los estudiantes.

                    </p>


                    <form
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <div class="mb-3">

                            <label
                                for="archivo_excel"
                                class="form-label"
                            >

                                Archivo Excel

                            </label>


                            <input
                                type="file"
                                name="archivo_excel"
                                id="archivo_excel"
                                class="form-control"
                                accept=".xlsx"
                                required
                            >

                            <div class="form-text">

                                Solo archivos .xlsx. Máximo 5 MB.

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-upload"></i>

                            Importar estudiantes

                        </button>

                    </form>

                </div>

            </div>



            <!-- =================================================
                 RESULTADOS
            ================================================== -->

            <?php if ($importados > 0 || $omitidos > 0): ?>

                <div class="card tarjeta mb-4">

                    <div class="card-body p-4">

                        <h4>

                            <i class="bi bi-bar-chart"></i>

                            Resultado de la importación

                        </h4>


                        <div class="row mt-3">


                            <div class="col-md-6 mb-3">

                                <div class="alert alert-success mb-0">

                                    <h5>

                                        <i class="bi bi-check-circle"></i>

                                        Importados

                                    </h5>

                                    <h2>

                                        <?php echo $importados; ?>

                                    </h2>

                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <div class="alert alert-warning mb-0">

                                    <h5>

                                        <i class="bi bi-exclamation-circle"></i>

                                        Omitidos

                                    </h5>

                                    <h2>

                                        <?php echo $omitidos; ?>

                                    </h2>

                                </div>

                            </div>


                        </div>


                        <!-- ERRORES -->

                        <?php if (count($errores) > 0): ?>

                            <hr>

                            <h5 class="text-danger">

                                <i class="bi bi-exclamation-triangle"></i>

                                Detalle de errores

                            </h5>


                            <div class="errores">

                                <ul class="list-group">

                                    <?php foreach ($errores as $error): ?>

                                        <li class="list-group-item list-group-item-danger">

                                            <?php
                                            echo htmlspecialchars($error);
                                            ?>

                                        </li>

                                    <?php endforeach; ?>

                                </ul>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endif; ?>



            <!-- =================================================
                 INFORMACIÓN
            ================================================== -->

            <div class="card tarjeta">

                <div class="card-body p-4">

                    <h4>

                        <i class="bi bi-info-circle"></i>

                        Formato del archivo

                    </h4>


                    <p>

                        El archivo Excel debe tener exactamente estas columnas:

                    </p>


                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead class="table-dark">

                                <tr>

                                    <th>Columna</th>

                                    <th>Ejemplo</th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr>

                                    <td>Tipo documento</td>

                                    <td>CC</td>

                                </tr>

                                <tr>

                                    <td>Documento</td>

                                    <td>1001234567</td>

                                </tr>

                                <tr>

                                    <td>Nombre Completo</td>

                                    <td>Juan Pérez</td>

                                </tr>

                                <tr>

                                    <td>Curso</td>

                                    <td>1103</td>

                                </tr>

                                <tr>

                                    <td>Sede</td>

                                    <td>SEDE PRINCIPAL</td>

                                </tr>

                                <tr>

                                    <td>Jornada</td>

                                    <td>UNICA / NOCTURNA</td>

                                </tr>

                                <tr>

                                    <td>Estado</td>

                                    <td>ACTIVO / RETIRADO</td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


        </div>

    </div>

</div>



<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>

<?php

$conexion->close();

?>