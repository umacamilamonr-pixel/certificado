<?php

// ==========================================
// INICIAR SESIÓN
// ==========================================

session_start();


// ==========================================
// SI YA HAY UNA SESIÓN ACTIVA
// ENVIAR AL DASHBOARD
// ==========================================

if (isset($_SESSION["usuario_id"]) && $_SESSION["rol"] === "SECRETARIA") {

    header("Location: dashboard.php");
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
// PROCESAR FORMULARIO
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Obtener datos del formulario

    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";


    // ==========================================
    // VALIDAR CAMPOS
    // ==========================================

    if ($usuario === "" || $password === "") {

        $mensaje = "Por favor, complete todos los campos.";
        $tipo_mensaje = "warning";

    } else {


        // ==========================================
        // BUSCAR USUARIO
        // ==========================================

        $sql = "SELECT
                    id_usuario,
                    nombre,
                    usuario,
                    password,
                    rol,
                    estado
                FROM usuarios
                WHERE usuario = ?
                LIMIT 1";


        $stmt = $conexion->prepare($sql);


        if ($stmt) {

            $stmt->bind_param("s", $usuario);

            $stmt->execute();

            $resultado = $stmt->get_result();


            // ==========================================
            // VERIFICAR SI EXISTE EL USUARIO
            // ==========================================

            if ($resultado->num_rows === 1) {

                $datos_usuario = $resultado->fetch_assoc();


                // ==========================================
                // VERIFICAR ESTADO
                // ==========================================

                if ($datos_usuario["estado"] !== "ACTIVO") {

                    $mensaje = "El usuario se encuentra inactivo.";
                    $tipo_mensaje = "danger";

                }

                // ==========================================
                // VERIFICAR ROL
                // ==========================================

                elseif ($datos_usuario["rol"] !== "SECRETARIA") {

                    $mensaje = "El usuario no tiene permisos para ingresar a este módulo.";
                    $tipo_mensaje = "danger";

                }

                // ==========================================
                // VERIFICAR CONTRASEÑA
                // ==========================================

                elseif (
                    password_verify(
                        $password,
                        $datos_usuario["password"]
                    )
                ) {


                    // ==========================================
                    // CREAR SESIÓN
                    // ==========================================

                    session_regenerate_id(true);


                    $_SESSION["usuario_id"] =
                        $datos_usuario["id_usuario"];


                    $_SESSION["usuario_nombre"] =
                        $datos_usuario["nombre"];


                    $_SESSION["usuario"] =
                        $datos_usuario["usuario"];


                    $_SESSION["rol"] =
                        $datos_usuario["rol"];


                    // ==========================================
                    // ENVIAR AL DASHBOARD
                    // ==========================================

                    header("Location: dashboard.php");

                    exit();

                } else {

                    $mensaje = "Usuario o contraseña incorrectos.";
                    $tipo_mensaje = "danger";

                }

            } else {

                $mensaje = "Usuario o contraseña incorrectos.";
                $tipo_mensaje = "danger";

            }


            $stmt->close();

        } else {

            $mensaje = "No fue posible realizar la consulta.";
            $tipo_mensaje = "danger";

        }

    }

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
        Acceso Secretaría Académica
    </title>


    <!-- ==========================================
         BOOTSTRAP 5
    ========================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
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

            min-height: 100vh;

            display: flex;

            flex-direction: column;

        }


        .login-container {

            flex: 1;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px 15px;

        }


        .login-card {

            width: 100%;

            max-width: 430px;

            border: none;

            border-radius: 15px;

        }


        .login-icon {

            width: 80px;

            height: 80px;

            margin: 0 auto 20px;

            border-radius: 50%;

            background-color: #0d6efd;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 35px;

        }


        .login-title {

            font-weight: 600;

        }


        .form-control {

            padding: 12px;

        }


        .btn-login {

            padding: 12px;

            font-size: 16px;

        }

    </style>

</head>


<body>


<!-- ==========================================
     BARRA SUPERIOR
========================================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <a
            href="../estudiante/index.php"
            class="navbar-brand"
        >

            Sistema de Certificados Escolares

        </a>

    </div>

</nav>


<!-- ==========================================
     CONTENIDO LOGIN
========================================== -->

<div class="login-container">

    <div class="card shadow login-card">


        <div class="card-body p-4 p-md-5">


            <!-- ==========================================
                 ICONO
            ========================================== -->

            <div class="login-icon">

                🔐

            </div>


            <!-- ==========================================
                 TÍTULO
            ========================================== -->

            <div class="text-center mb-4">

                <h3 class="login-title">

                    Secretaría Académica

                </h3>

                <p class="text-muted mb-0">

                    Ingrese sus datos para continuar

                </p>

            </div>


            <!-- ==========================================
                 MENSAJE
            ========================================== -->

            <?php if ($mensaje !== ""): ?>

                <div
                    class="alert alert-<?php echo $tipo_mensaje; ?>"
                    role="alert"
                >

                    <?php
                    echo htmlspecialchars($mensaje);
                    ?>

                </div>

            <?php endif; ?>


            <!-- ==========================================
                 FORMULARIO
            ========================================== -->

            <form
                method="POST"
                action="login.php"
            >


                <!-- USUARIO -->

                <div class="mb-3">

                    <label
                        for="usuario"
                        class="form-label fw-semibold"
                    >

                        Usuario

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="usuario"
                        name="usuario"
                        placeholder="Ingrese su usuario"
                        maxlength="50"
                        autocomplete="username"
                        required
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["usuario"] ?? ""
                            );
                        ?>"
                    >

                </div>


                <!-- CONTRASEÑA -->

                <div class="mb-4">

                    <label
                        for="password"
                        class="form-label fw-semibold"
                    >

                        Contraseña

                    </label>

                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Ingrese su contraseña"
                        autocomplete="current-password"
                        required
                    >

                </div>


                <!-- BOTÓN -->

                <div class="d-grid">

                    <button
                        type="submit"
                        class="btn btn-primary btn-login"
                    >

                        Ingresar

                    </button>

                </div>


            </form>


            <!-- ==========================================
                 VOLVER
            ========================================== -->

            <div class="text-center mt-4">

                <a
                    href="../estudiante/index.php"
                    class="text-decoration-none"
                >

                    ← Volver a solicitud de estudiante

                </a>

            </div>


        </div>

    </div>

</div>


<!-- ==========================================
     FOOTER
========================================== -->

<footer class="footer">

    <p class="mb-0">

        Sistema de Solicitud de Certificados Escolares

    </p>

    <small>

        &copy; <?php echo date("Y"); ?>
        Institución Educativa

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