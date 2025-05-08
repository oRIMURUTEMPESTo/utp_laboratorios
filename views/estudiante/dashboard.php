<!-- filepath: c:\xampp\htdocs\sistema_laboratorios\views\estudiante\dashboard.php -->
<?php
require_once '../../database.php';
require_once '../partials/header.php';

$db = new Database();
$conn = $db->conectar();

// Obtener laboratorios disponibles
$sql = "SELECT LaboratorioID, Nombre, Descripcion, Capacidad FROM laboratorios WHERE Estado = 'Operativo'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$laboratorios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Estudiante</title>
    <link rel="stylesheet" href="../../styles/style.css"> <!-- Agregar el CSS -->
    <style>
        /* Estilos para encuadrar los laboratorios */
        .laboratorios {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .laboratorio {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 16px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            background-color: #f9f9f9;
        }

        .laboratorio h3 {
            margin: 0 0 10px;
            font-size: 1.5em;
            color: #333;
        }

        .laboratorio p {
            margin: 5px 0;
            color: #555;
        }

        .laboratorio a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .laboratorio a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['Nombre']); ?></h1>
    <h2>Selecciona un laboratorio</h2>
    <div class="laboratorios">
        <?php foreach ($laboratorios as $lab): ?>
            <div class="laboratorio">
                <h3><?php echo htmlspecialchars($lab['Nombre']); ?></h3>
                <p><?php echo htmlspecialchars($lab['Descripcion']); ?></p>
                <p><strong>Capacidad:</strong> <?php echo $lab['Capacidad']; ?></p>
                <a href="reservar_laboratorio.php?laboratorio_id=<?php echo $lab['LaboratorioID']; ?>">Reservar</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>