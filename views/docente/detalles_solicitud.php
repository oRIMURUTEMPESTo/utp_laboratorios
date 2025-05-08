<?php
session_start();
require_once '../../database.php';
require_once '../partials/header_docente.php'; // Agregar el header del docente

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
            u.Nombre AS NombreEstudiante, 
            u.Matricula, 
            l.Nombre AS NombreLaboratorio, 
            l.LaboratorioID, 
            s.FechaSolicitud, 
            s.HoraInicio, 
            s.CantidadHoras, 
            s.MotivoUso, 
            s.Estado 
        FROM solicitudes s
        JOIN usuarios u ON s.UsuarioID = u.UsuarioID
        JOIN laboratorios l ON s.LaboratorioID = l.LaboratorioID
        WHERE s.SolicitudID = :solicitud_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
$stmt->execute();
$solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitud) {
    die("Error: Solicitud no encontrada.");
}

// Obtener los compañeros asociados a la solicitud
$sql_companeros = "SELECT NombreCompleto, Matricula FROM solicitudescompaneros WHERE SolicitudID = :solicitud_id";
$stmt_companeros = $conn->prepare($sql_companeros);
$stmt_companeros->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
$stmt_companeros->execute();
$companeros = $stmt_companeros->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Solicitud</title>
    <link rel="stylesheet" href="../../styles/style.css"> <!-- Agregar el CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet"> <!-- FullCalendar CSS -->
    <style>
        #calendar {
            max-width: 900px;
            margin: 20px auto;
            height: 600px;
            font-size: 0.9rem;
            background-color: #f4f8fb;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Detalles de la Solicitud</h1>
    <p><strong>ID de la Solicitud:</strong> <?php echo $solicitud['SolicitudID']; ?></p>
    <p><strong>Estudiante:</strong> <?php echo htmlspecialchars($solicitud['NombreEstudiante']); ?> (Matrícula: <?php echo $solicitud['Matricula']; ?>)</p>
    <p><strong>Laboratorio:</strong> <?php echo htmlspecialchars($solicitud['NombreLaboratorio']); ?></p>
    <p><strong>Fecha de Solicitud:</strong> <?php echo $solicitud['FechaSolicitud']; ?></p>
    <p><strong>Hora de Inicio:</strong> <?php echo $solicitud['HoraInicio']; ?></p>
    <p><strong>Duración:</strong> <?php echo $solicitud['CantidadHoras']; ?> horas</p>
    <p><strong>Motivo:</strong> <?php echo htmlspecialchars($solicitud['MotivoUso']); ?></p>
    <p><strong>Estado:</strong> <?php echo $solicitud['Estado']; ?></p>

    <h2>Compañeros Asociados</h2>
    <?php if (count($companeros) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Matrícula</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companeros as $comp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comp['NombreCompleto']); ?></td>
                        <td><?php echo htmlspecialchars($comp['Matricula']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay compañeros asociados a esta solicitud.</p>
    <?php endif; ?>

    <h2>Calendario del Laboratorio</h2>
    <div id="calendar"></div>

    <h2>Acciones</h2>
    <form method="POST" action="../../scripts/gestionar_solicitud.php">
        <input type="hidden" name="solicitud_id" value="<?php echo $solicitud['SolicitudID']; ?>">
        <button type="submit" name="accion" value="aprobar">Aprobar</button>
        <button type="submit" name="accion" value="rechazar">Rechazar</button>
    </form>
    <a href="modificar_horario.php?solicitud_id=<?php echo $solicitud['SolicitudID']; ?>">Modificar Horario</a>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script> <!-- FullCalendar JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay'
                },
                events: '../../scripts/api_calendario.php?laboratorio_id=<?php echo $solicitud['LaboratorioID']; ?>',
                eventColor: '#378006',
                eventDidMount: function (info) {
                    if (info.event.extendedProps.estado === 'pendiente') {
                        info.el.style.backgroundColor = 'yellow';
                        info.el.style.color = 'black';
                    } else if (info.event.extendedProps.estado === 'reservado') {
                        info.el.style.backgroundColor = 'red';
                        info.el.style.color = 'white';
                    }
                },
                loading: function (isLoading) {
                    if (isLoading) {
                        console.log('Cargando eventos...');
                    } else {
                        console.log('Eventos cargados.');
                    }
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>