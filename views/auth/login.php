<?php
session_start();
if (isset($_SESSION['usuario'])) {
    // Redirigir al dashboard según el rol
    if ($_SESSION['rol'] == 'docente') {
        header("Location: ../../views/docente/dashboard.php");
    } else {
        header("Location: ../../views/estudiante/dashboard.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Sistema de Laboratorios</title>
    <link rel="stylesheet" href="../../public/css/estilos.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form action="../../controllers/AuthController.php" method="POST">
            <label for="usuario">Usuario (Matrícula o CI):</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="estudiante">Estudiante</option>
                <option value="docente">Docente</option>
            </select>

            <input type="submit" value="Ingresar">
        </form>

        <?php if (isset($_GET['error'])): ?>
            <p class="error">Credenciales incorrectas. Inténtalo de nuevo.</p>
        <?php endif; ?>
    </div>
</body>
</html>
