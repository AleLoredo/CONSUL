<?php
// Iniciar la sesión para acceder a las variables de sesión
// session_start();

// // Verificar si el usuario está logueado
// if (!isset($_SESSION['user_id'])) {
//     // Si no está logueado, redirigir al login
//     header('Location: index.php');
//     exit;
// }
?>

<?php

// Configurar la duración de la sesión a 12 horas (en segundos)
ini_set('session.gc_maxlifetime', 43200); // 12 horas = 43200 segundos
ini_set('session.cookie_lifetime', 43200); // Duración de la cookie de sesión

// Iniciar la sesión
session_start();

// Regenerar el ID de sesión periódicamente para mayor seguridad
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} elseif (time() - $_SESSION['CREATED'] > 43200) {
    // Si la sesión supera las 12 horas, destruirla y forzar un nuevo inicio de sesión
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    // Si no está logueado, redirigir al login
    header('Location: index.php');
    exit;
}
?>