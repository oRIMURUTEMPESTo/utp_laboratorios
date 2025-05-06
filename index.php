<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    // Redirigir según el rol
    if ($_SESSION['rol'] == 'docente') {
        header("Location: views/docente/dashboard.php");
    } else {
        header("Location: views/estudiante/dashboard.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión - Sistema de Laboratorios</title>
    <link rel="stylesheet" href="public/css/estilo.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form action="controllers/AuthController.php" method="POST">
            <label for="usuario">Usuario (Matrícula o CI):</label>
            <input type="text" name="usuario" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <label for="rol">Rol:</label>
            <select name="rol" required>
                <option value="estudiante">Estudiante</option>
                <option value="docente">Docente</option>
            </select>

            <input type="submit" value="Ingresar">
        </form>

        <?php
        if (isset($_GET['error'])) {
            echo "<p class='error'>Credenciales incorrectas. Inténtalo de nuevo.</p>";
        }
        ?>
    </div>
</body>
</html>