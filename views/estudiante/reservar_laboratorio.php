<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../database.php'; // Ruta corregida
require_once '../partials/header.php';

// Verificar si el usuario ha iniciado sesión y es estudiante
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['Rol'] !== 'Estudiante') {
    header("Location: ../../login.php");
    exit;
}

// Verificar si se recibió el ID del laboratorio
if (!isset($_GET['laboratorio_id'])) {
    die("Error: Laboratorio no especificado.");
}

$laboratorio_id = $_GET['laboratorio_id'];

$db = new Database();
$conn = $db->conectar();

// Obtener información del laboratorio
$sql = "SELECT Nombre, Descripcion, Capacidad FROM laboratorios WHERE LaboratorioID = :laboratorio_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':laboratorio_id', $laboratorio_id, PDO::PARAM_INT);
$stmt->execute();
$laboratorio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$laboratorio) {
    die("Error: Laboratorio no encontrado.");
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que todos los campos estén presentes
    if (!isset($_POST['fecha'], $_POST['hora_inicio'], $_POST['hora_fin'], $_POST['motivo']) ||
        empty($_POST['fecha']) || empty($_POST['hora_inicio']) || empty($_POST['hora_fin']) || empty($_POST['motivo'])) {
        die("Por favor, completa todos los campos.");
    }

    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $motivo = $_POST['motivo'];
    $usuario_id = $_SESSION['usuario']['UsuarioID'];
    $companeros = isset($_POST['companeros']) ? $_POST['companeros'] : [];

    // Llamar al procedimiento almacenado para registrar la solicitud
    $sql = "CALL registrar_solicitud2(:laboratorio_id, :fecha, :hora_inicio, :hora_fin, :motivo, :usuario_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':laboratorio_id', $laboratorio_id, PDO::PARAM_INT);
    $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
    $stmt->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
    $stmt->bindParam(':hora_fin', $hora_fin, PDO::PARAM_STR);
    $stmt->bindParam(':motivo', $motivo, PDO::PARAM_STR);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener el ID de la solicitud recién creada
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $solicitud_id = $result['SolicitudID'];

    // Cerrar el cursor para liberar el conjunto de resultados
    $stmt->closeCursor();

    if (!$solicitud_id) {
        die("Error: No se pudo obtener el ID de la solicitud.");
    }

    // Insertar los compañeros en la tabla solicitudescompaneros
    $sql_companeros = "INSERT INTO solicitudescompaneros (SolicitudID, NombreCompleto, Matricula) VALUES (:solicitud_id, :nombre_completo, :matricula)";
    $stmt_companeros = $conn->prepare($sql_companeros);

    foreach ($companeros as $comp) {
        if (!empty($comp['nombre']) && !empty($comp['matricula'])) {
            $stmt_companeros->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
            $stmt_companeros->bindParam(':nombre_completo', $comp['nombre'], PDO::PARAM_STR);
            $stmt_companeros->bindParam(':matricula', $comp['matricula'], PDO::PARAM_STR);
            $stmt_companeros->execute();
        }
    }

    // Redirigir al usuario a la página de revisión de solicitudes
    header("Location: revisar_respuesta.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Laboratorio</title>
    <link rel="stylesheet" href="../../styles/style.css"> <!-- Agregar el CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet"> <!-- FullCalendar CSS -->
</head>
<body>
    <h1>Reservar Laboratorio</h1>
    <h2><?php echo htmlspecialchars($laboratorio['Nombre']); ?></h2>
    <p><?php echo htmlspecialchars($laboratorio['Descripcion']); ?></p>
    <p><strong>Capacidad:</strong> <?php echo $laboratorio['Capacidad']; ?></p>

    <h3>Calendario de Reservas</h3>
    <div id="calendar"></div>

    <h3>Formulario de Reserva</h3>
    <form id="reservaForm" method="POST" action="">
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
        <label for="companeros">Compañeros:</label>
        <div id="companerosContainer">
            <div>
                <input type="text" name="companeros[0][nombre]" placeholder="Nombre completo" required>
                <input type="text" name="companeros[0][matricula]" placeholder="Matrícula" required>
            </div>
        </div>
        <button type="button" id="addCompanero">Añadir compañero</button>
        <br><br>
        <button type="submit">Reservar</button>
    </form>

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
                events: '../../scripts/api_calendario.php?laboratorio_id=<?php echo $laboratorio_id; ?>',
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

            // Añadir campos dinámicos para compañeros
            document.getElementById('addCompanero').addEventListener('click', function () {
                var container = document.getElementById('companerosContainer');
                var index = container.children.length;
                var div = document.createElement('div');
                div.innerHTML = `
                    <input type="text" name="companeros[${index}][nombre]" placeholder="Nombre completo" required>
                    <input type="text" name="companeros[${index}][matricula]" placeholder="Matrícula" required>
                `;
                container.appendChild(div);
            });
        });
    </script>
</body>
</html>