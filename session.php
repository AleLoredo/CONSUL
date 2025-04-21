<?php
// Iniciar la sesión para acceder a las variables de sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    // Si no está logueado, redirigir al login
    header('Location: index.php');
    exit;
}
?>