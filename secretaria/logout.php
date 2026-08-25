<?php

// ==========================================
// INICIAR SESIÓN
// ==========================================

session_start();


// ==========================================
// ELIMINAR TODAS LAS VARIABLES DE SESIÓN
// ==========================================

$_SESSION = [];


// ==========================================
// ELIMINAR LA COOKIE DE SESIÓN
// ==========================================

if (ini_get("session.use_cookies")) {

    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $parametros["path"],
        $parametros["domain"],
        $parametros["secure"],
        $parametros["httponly"]
    );

}


// ==========================================
// DESTRUIR LA SESIÓN
// ==========================================

session_destroy();


// ==========================================
// REDIRIGIR AL LOGIN
// ==========================================

header("Location: login.php");

exit();

?>