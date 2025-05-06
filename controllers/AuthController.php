<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $rol = trim($_POST['rol']);

    // Validación de campos vacíos
    if (empty($usuario) || empty($password) || empty($rol)) {
        header("Location: ../views/auth/error_login.php?error=campos_vacios");
        exit;
    }

    // Validación de rol permitido
    if (!in_array($rol, ['estudiante', 'docente'])) {
        header("Location: ../views/auth/error_login.php?error=rol_invalido");
        exit;
    }

    // Validar que se puede conectar a la base de datos
    if (!$conn) {
        header("Location: ../views/auth/error_login.php?error=conexion_bd");
        exit;
    }

    // Definir el campo según el rol
    $campoBusqueda = $rol === 'estudiante' ? 'Matricula' : 'Credencial';

    // Asegurarse de que el campo es seguro (solo esos dos)
    if (!in_array($campoBusqueda, ['Matricula', 'Credencial'])) {
        header("Location: ../views/auth/error_login.php?error=campo_invalido");
        exit;
    }

    // Preparar y ejecutar consulta
    $sql = "SELECT * FROM usuarios WHERE $campoBusqueda = ? AND Contrasena = ? AND Rol = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        header("Location: ../views/auth/error_login.php?error=stmt_fallo");
        exit;
    }

    $stmt->bind_param("sss", $usuario, $password, $rol);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $usuarioData = $resultado->fetch_assoc();
        $_SESSION['usuario_id'] = $usuarioData['UsuarioID'];
        $_SESSION['nombre'] = $usuarioData['Nombre'];
        $_SESSION['rol'] = $usuarioData['Rol'];

        // Redirigir según rol
        if ($rol === 'estudiante') {
            header("Location: ../views/estudiante/dashboard.php");
        } else {
            header("Location: ../views/docente/dashboard.php");
        }
        exit;
    } else {
        // Usuario no encontrado
        header("Location: ../views/auth/error_login.php?error=credenciales_invalidas");
        exit;
    }

} else {
    // Si no es POST, redirigir al login
    header("Location: ../views/auth/login.php");
    exit;
}
