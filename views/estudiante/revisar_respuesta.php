<?php
session_start();
require_once '../../database.php';
require_once '../partials/header.php'; // Agregar el header

// Verificar si el usuario ha iniciado sesión y es estudiante
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['Rol'] !== 'Estudiante') {
    header("Location: ../../login.php");
    exit;
}

$db = new Database();
$conn = $db->conectar();

// Obtener las solicitudes del estudiante
$usuario_id = $_SESSION['usuario']['UsuarioID'];
$sql = "SELECT 
            s.SolicitudID, 
            l.Nombre AS NombreLaboratorio, 
            s.FechaSolicitud, 
            s.HoraInicio, 
            s.CantidadHoras, 
            s.Estado 
        FROM solicitudes s
        JOIN laboratorios l ON s.LaboratorioID = l.LaboratorioID
        WHERE s.UsuarioID = :usuario_id
        ORDER BY s.FechaSolicitud DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Respuesta</title>
    <link rel="stylesheet" href="../../styles/style.css"> <!-- Agregar el CSS -->
</head>
<body>
    <h1>Mis Solicitudes</h1>
    <?php if (count($solicitudes) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Laboratorio</th>
                    <th>Fecha</th>
                    <th>Hora Inicio</th>
                    <th>Duración (Horas)</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <tr>
                        <td><?php echo $solicitud['SolicitudID']; ?></td>
                        <td><?php echo htmlspecialchars($solicitud['NombreLaboratorio']); ?></td>
                        <td><?php echo $solicitud['FechaSolicitud']; ?></td>
                        <td><?php echo $solicitud['HoraInicio']; ?></td>
                        <td><?php echo $solicitud['CantidadHoras']; ?></td>
                        <td><?php echo $solicitud['Estado']; ?></td>
                        <td>
                            <?php if ($solicitud['Estado'] === 'Pendiente'): ?>
                                <a href="../../scripts/cancelar_solicitud.php?solicitud_id=<?php echo $solicitud['SolicitudID']; ?>">Cancelar</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tienes solicitudes registradas.</p>
    <?php endif; ?>
</body>
</html>