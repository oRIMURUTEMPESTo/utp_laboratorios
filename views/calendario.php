<?php
session_start();
require_once '../database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

// Obtener detalles de la solicitud
$solicitud_id = $_GET['id'];
$query = "SELECT * FROM solicitudes WHERE id = $solicitud_id";
$result = $conn->query($query);
$solicitud = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Solicitud</title>
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
            height: 600px; /* Define una altura para el calendario */
        }
    </style>
</head>
<body>
    <h1>Detalles de la Solicitud</h1>
    <div id="calendar"></div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
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