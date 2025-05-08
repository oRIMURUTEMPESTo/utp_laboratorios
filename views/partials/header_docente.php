<!-- filepath: c:\xampp\htdocs\sistema_laboratorios\views\partials\header_docente.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['Rol'] !== 'Docente') {
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
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="../docente/dashboard.php">Inicio</a></li>
                <li><a href="../docente/calendario_general.php">Calendario General</a></li>
                <li><a href="../docente/revisar_solicitudes.php">Revisar Solicitudes</a></li>
                <li><a href="../../logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>