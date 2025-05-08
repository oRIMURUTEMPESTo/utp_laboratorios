<?php
session_start();
require_once '../database.php';

// Verificar si el usuario ha iniciado sesión y es estudiante
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['Rol'] !== 'Estudiante') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laboratorio_id = $_POST['laboratorio'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $motivo = $_POST['motivo'];
    $usuario_id = $_SESSION['usuario']['UsuarioID'];

    // Validar que los campos no estén vacíos
    if (empty($laboratorio_id) || empty($fecha) || empty($hora_inicio) || empty($hora_fin) || empty($motivo)) {
        die("Por favor, completa todos los campos.");
    }

    // Calcular la diferencia de horas en PHP
    $hora_inicio_dt = new DateTime($hora_inicio);
    $hora_fin_dt = new DateTime($hora_fin);
    $intervalo = $hora_inicio_dt->diff($hora_fin_dt);
    $cantidad_horas = $intervalo->h;

    // Validar que la cantidad de horas sea mayor a 0
    if ($cantidad_horas <= 0) {
        die("La hora de fin debe ser mayor que la hora de inicio.");
    }

    $db = new Database();
    $conn = $db->conectar();

    // Verificar si el horario está disponible
    $sql = "SELECT * FROM reservas WHERE LaboratorioID = :laboratorio_id AND FechaReserva = :fecha
            AND ((HoraInicio <= :hora_inicio AND HoraFin > :hora_inicio) OR (HoraInicio < :hora_fin AND HoraFin >= :hora_fin))";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':laboratorio_id', $laboratorio_id);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':hora_inicio', $hora_inicio);
    $stmt->bindParam(':hora_fin', $hora_fin);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        die("El horario seleccionado no está disponible.");
    }

    // Insertar la solicitud en la base de datos
    $sql = "INSERT INTO solicitudes (UsuarioID, LaboratorioID, MotivoUso, FechaSolicitud, HoraInicio, CantidadHoras, Estado)
            VALUES (:usuario_id, :laboratorio_id, :motivo, :fecha, :hora_inicio, :cantidad_horas, 'Pendiente')";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':laboratorio_id', $laboratorio_id);
    $stmt->bindParam(':motivo', $motivo);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':hora_inicio', $hora_inicio);
    $stmt->bindParam(':cantidad_horas', $cantidad_horas);

    if ($stmt->execute()) {
        echo "Solicitud enviada correctamente.";
        header("Location: ../views/estudiante/revisar_respuesta.php");
        exit;
    } else {
        echo "Error al enviar la solicitud.";
    }
}
?>