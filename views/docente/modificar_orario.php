<!-- filepath: c:\xampp\htdocs\sistema_laboratorios\views\docente\modificar_horario.php -->
<?php
session_start();
require_once '../../database.php';
require_once '../partials/header_docente.php'; // Header del docente

// Verificar si el usuario ha iniciado sesión y es docente
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['Rol'] !== 'Docente') {
    header("Location: ../../login.php");
    exit;
}

// Verificar si se recibió el ID de la solicitud
if (!isset($_GET['solicitud_id'])) {
    die("Error: Solicitud no especificada.");
}

$solicitud_id = $_GET['solicitud_id'];

$db = new Database();
$conn = $db->conectar();

// Obtener detalles de la solicitud
$sql = "SELECT 
            s.SolicitudID, 
            l.Nombre AS NombreLaboratorio, 
            s.FechaSolicitud, 
            s.HoraInicio, 
            s.CantidadHoras 
        FROM solicitudes s
        JOIN laboratorios l ON s.LaboratorioID = l.LaboratorioID
        WHERE s.SolicitudID = :solicitud_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
$stmt->execute();
$solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitud) {
    die("Error: Solicitud no encontrada.");
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hora_inicio = $_POST['hora_inicio'];
    $cantidad_horas = $_POST['cantidad_horas'];

    // Validar los datos
    if (empty($hora_inicio) || empty($cantidad_horas)) {
        die("Error: Los campos 'Hora de Inicio' y 'Duración' son obligatorios.");
    }

    // Actualizar la solicitud con el nuevo horario
    $sql = "UPDATE solicitudes SET HoraInicio = :hora_inicio, CantidadHoras = :cantidad_horas WHERE SolicitudID = :solicitud_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
    $stmt->bindParam(':cantidad_horas', $cantidad_horas, PDO::PARAM_INT);
    $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirigir de vuelta a detalles_solicitud.php
    header("Location: detalles_solicitud.php?solicitud_id=$solicitud_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Horario</title>
    <link rel="stylesheet" href="../../styles/style.css"> <!-- Agregar el CSS -->
</head>
<body>
    <h1>Modificar Horario</h1>
    <p><strong>Laboratorio:</strong> <?php echo htmlspecialchars($solicitud['NombreLaboratorio']); ?></p>
    <p><strong>Fecha:</strong> <?php echo $solicitud['FechaSolicitud']; ?></p>

    <form method="POST" action="">
        <label for="hora_inicio">Hora de Inicio:</label>
        <input type="time" id="hora_inicio" name="hora_inicio" value="<?php echo $solicitud['HoraInicio']; ?>" required>
        <br>
        <label for="cantidad_horas">Duración (Horas):</label>
        <input type="number" id="cantidad_horas" name="cantidad_horas" value="<?php echo $solicitud['CantidadHoras']; ?>" min="1" required>
        <br>
        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>
