<!-- filepath: c:\xampp\htdocs\sistema_laboratorios\views\docente\calendario_general.php -->
<?php
require_once '../../database.php';
require_once '../partials/header_docente.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario General</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
            height: 600px;
            font-size: 0.8rem;
            background-color: #d4edda; /* Fondo verde claro */
            border: 1px solid #c3e6cb; /* Borde verde */
            padding: 10px;
            border-radius: 5px;
        }
        .fc-toolbar {
            font-size: 0.9rem;
        }
        .fc-event {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <h1>Calendario General</h1>
    <div id="calendar"></div>

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
                events: '../../scripts/api_calendario_general.php',
                eventColor: '#378006',
                eventDidMount: function (info) {
                    console.log('Evento cargado:', info.event.title, info.event.start, info.event.end);
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