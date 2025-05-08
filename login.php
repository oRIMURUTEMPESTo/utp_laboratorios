<?php
require_once 'database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $rol = $_POST['rol']; // 'estudiante' o 'docente'

    if (empty($usuario) || empty($password) || empty($rol)) {
        die("Por favor, completa todos los campos.");
    }

    $db = new Database();
    $conn = $db->conectar();

    if (!$conn) {
        die("Error al conectar con la base de datos.");
    }

    // Determinar el campo de búsqueda según el rol
    $campoBusqueda = $rol === 'estudiante' ? 'Matricula' : 'Credencial';

    // Consulta SQL para verificar las credenciales
    $sql = "SELECT * FROM usuarios WHERE $campoBusqueda = :usuario AND Contrasena = :password AND Rol = :rol";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar la consulta.");
    }

    // Vincular parámetros
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':rol', $rol);

    // Ejecutar la consulta
    $stmt->execute();

    // Verificar si se encontró un usuario
    if ($stmt->rowCount() === 1) {
        $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['usuario'] = $usuarioData;

        // Redirigir según el rol
        if ($rol === 'estudiante') {
            header("Location: views/estudiante/dashboard.php");
        } elseif ($rol === 'docente') {
            header("Location: views/docente/dashboard.php");
        }
        exit;
    } else {
        echo "Credenciales incorrectas o usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/style.css"> <!-- Agregar el CSS -->
        <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f8fb;
        }

        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h1 {
            color: #007bff;
            margin-bottom: 20px;
        }

        .login-container label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }

        .login-container input,
        .login-container select,
        .login-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .login-container button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <form method="POST" action="login.php">
            <label for="usuario">Usuario (Matrícula o Credencial):</label>
            <input type="text" id="usuario" name="usuario" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="estudiante">Estudiante</option>
                <option value="docente">Docente</option>
            </select>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>