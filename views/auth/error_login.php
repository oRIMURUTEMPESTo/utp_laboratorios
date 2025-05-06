<?php
// Obtener el tipo de error desde la URL
$error = $_GET['error'] ?? '';
$mensaje = 'Ha ocurrido un error desconocido.';

switch ($error) {
    case 'campos_vacios':
        $mensaje = 'Por favor, completa todos los campos.';
        break;
    case 'rol_invalido':
        $mensaje = 'Rol inválido seleccionado.';
        break;
    case 'conexion_bd':
        $mensaje = 'Error al conectar con la base de datos.';
        break;
    case 'campo_invalido':
        $mensaje = 'Campo de búsqueda inválido.';
        break;
    case 'stmt_fallo':
        $mensaje = 'Error interno al preparar la consulta.';
        break;
    case 'credenciales_invalidas':
        $mensaje = 'Usuario o contraseña incorrectos.';
        break;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error de inicio de sesión</title>
    <link rel="stylesheet" href="../../public/css/estilos.css">
</head>
<body>
    <div class="error-container">
        <h2>Error de inicio de sesión</h2>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
        <a href="login.php">Volver al login</a>
    </div>
</body>
</html>
