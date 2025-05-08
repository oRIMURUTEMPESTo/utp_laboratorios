<?php
session_start();
require_once '../../database.php';

// Verificar si el usuario ha iniciado sesión y es docente
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['Rol'] !== 'Docente') {
    header("Location: ../../login.php");
    exit;
}

$db = new Database();
$conn = $db->conectar();

// Obtener las solicitudes pendientes
$sql = "SELECT 
            s.SolicitudID, 
            u.Nombre AS NombreEstudiante, 
            l.Nombre AS NombreLaboratorio, 
            s.FechaSolicitud, 
            s.HoraInicio, 
            s.CantidadHoras, 
            s.Estado 
        FROM solicitudes s
        JOIN usuarios u ON s.UsuarioID = u.UsuarioID
        JOIN laboratorios l ON s.LaboratorioID = l.LaboratorioID
        WHERE s.Estado = 'Pendiente'
        ORDER BY s.FechaSolicitud ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Solicitudes</title>
    <link rel="stylesheet" href="../../styles/style.css"> <!-- Agregar el CSS -->
</head>
<body>
    <?php require_once '../partials/header_docente.php'; // Agregar el header del docente ?>
    <h1>Solicitudes Pendientes</h1>
    <?php if (count($solicitudes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Laboratorio</th>
                    <th>Fecha</th>
                    <th>Hora Inicio</th>
                    <th>Duración (Horas)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <tr>
                        <td><?php echo $solicitud['SolicitudID']; ?></td>
                        <td><?php echo htmlspecialchars($solicitud['NombreEstudiante']); ?></td>
                        <td><?php echo htmlspecialchars($solicitud['NombreLaboratorio']); ?></td>
                        <td><?php echo $solicitud['FechaSolicitud']; ?></td>
                        <td><?php echo $solicitud['HoraInicio']; ?></td>
                        <td><?php echo $solicitud['CantidadHoras']; ?></td>
                        <td>
                            <a href="detalles_solicitud.php?solicitud_id=<?php echo $solicitud['SolicitudID']; ?>">Ver Detalles</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay solicitudes pendientes.</p>
    <?php endif; ?>
</body>
</html>