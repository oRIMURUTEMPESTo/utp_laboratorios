<?php
session_start();
require_once '../../database.php';

// Verificar si el usuario ha iniciado sesión y es estudiante
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['Rol'] !== 'Estudiante') {
    header("Location: ../../login.php");
    exit;
}

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
    <title>Reservar Laboratorio</title>
</head>
<body>
    <h1>Reservar Laboratorio</h1>
    <form method="POST" action="../../scripts/reservar_laboratorio.php">
        <label for="laboratorio">Selecciona un laboratorio:</label>
        <select id="laboratorio" name="laboratorio" required>
            <?php foreach ($laboratorios as $lab): ?>
                <option value="<?php echo $lab['LaboratorioID']; ?>">
                    <?php echo $lab['Nombre'] . " (Capacidad: " . $lab['Capacidad'] . ")"; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="fecha">Fecha de reserva:</label>
        <input type="date" id="fecha" name="fecha" required>
        <br><br>
        <label for="hora_inicio">Hora de inicio:</label>
        <input type="time" id="hora_inicio" name="hora_inicio" required>
        <br><br>
        <label for="hora_fin">Hora de fin:</label>
        <input type="time" id="hora_fin" name="hora_fin" required>
        <br><br>
        <label for="motivo">Motivo de la reserva:</label>
        <textarea id="motivo" name="motivo" required></textarea>
        <br><br>
        <button type="submit">Reservar</button>
    </form>
</body>
</html>