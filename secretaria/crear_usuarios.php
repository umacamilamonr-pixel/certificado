<?php

// ==========================================
// CREAR USUARIO DE SECRETARÍA
// ARCHIVO TEMPORAL
// ==========================================

require_once "../config/conexion.php";


// ==========================================
// DATOS DEL USUARIO
// ==========================================

$nombre = "Secretaría Académica";

$usuario = "secretaria";

$password = "123456";

$rol = "SECRETARIA";

$estado = "ACTIVO";


// ==========================================
// GENERAR CONTRASEÑA SEGURA
// ==========================================

$password_hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// ==========================================
// VERIFICAR SI EL USUARIO YA EXISTE
// ==========================================

$sql_verificar = "SELECT id_usuario
                  FROM usuarios
                  WHERE usuario = ?
                  LIMIT 1";

$stmt_verificar = $conexion->prepare($sql_verificar);

$stmt_verificar->bind_param(
    "s",
    $usuario
);

$stmt_verificar->execute();

$resultado = $stmt_verificar->get_result();


if ($resultado->num_rows > 0) {

    echo "<h3>El usuario ya existe.</h3>";

    echo "<p>Usuario: <strong>"
        . htmlspecialchars($usuario)
        . "</strong></p>";

    echo "<p>No se creó un usuario duplicado.</p>";

    $stmt_verificar->close();

    $conexion->close();

    exit();

}

$stmt_verificar->close();


// ==========================================
// INSERTAR USUARIO
// ==========================================

$sql_insertar = "INSERT INTO usuarios
                (
                    nombre,
                    usuario,
                    password,
                    rol,
                    estado
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )";

$stmt = $conexion->prepare($sql_insertar);

$stmt->bind_param(
    "sssss",
    $nombre,
    $usuario,
    $password_hash,
    $rol,
    $estado
);


// ==========================================
// EJECUTAR
// ==========================================

if ($stmt->execute()) {

    echo "<h2>Usuario creado correctamente</h2>";

    echo "<p><strong>Nombre:</strong> "
        . htmlspecialchars($nombre)
        . "</p>";

    echo "<p><strong>Usuario:</strong> "
        . htmlspecialchars($usuario)
        . "</p>";

    echo "<p><strong>Contraseña:</strong> "
        . htmlspecialchars($password)
        . "</p>";

    echo "<p><strong>Rol:</strong> "
        . htmlspecialchars($rol)
        . "</p>";

    echo "<hr>";

    echo "<p style='color:red;'>
            IMPORTANTE: elimine este archivo
            crear_usuario.php después de ejecutarlo.
          </p>";

} else {

    echo "<h3>Error al crear el usuario.</h3>";

    echo "<p>"
        . htmlspecialchars($stmt->error)
        . "</p>";

}


$stmt->close();

$conexion->close();

?>