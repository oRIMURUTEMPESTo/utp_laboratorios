<!-- filepath: c:\xampp\htdocs\sistema_laboratorios\views\partials\header.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Laboratorios</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/style.css"> <!-- Agregar el CSS -->
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="../estudiante/dashboard.php">Inicio</a></li>
                <li><a href="../estudiante/revisar_respuesta.php">Mis Solicitudes</a></li>
                <li><a href="../../logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>