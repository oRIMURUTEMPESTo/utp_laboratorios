<!-- filepath: c:\xampp\htdocs\sistema_laboratorios\views\docente\dashboard_docente.php -->
<?php
require_once '../../database.php';
require_once '../partials/header.php';

$db = new Database();
$conn = $db->conectar();

// Obtener solicitudes pendientes
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

<head>
    <link rel="stylesheet" href="../../styles/style.css"> <!-- Agregar el CSS -->
</head>

<h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['Nombre']); ?></h1>
<h2>Solicitudes Pendientes</h2>

<?php if (count($solicitudes) > 0): ?>
    <table border="1">
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

<h2>Acceso rápido</h2>
<ul>
    <li><a href="revisar_solicitudes.php">Ver todas las solicitudes</a></li>
    <li><a href="calendario_general.php">Ver calendario general</a></li>
</ul>
</body>
</html>