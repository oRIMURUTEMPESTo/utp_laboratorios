<!-- filepath: c:\xampp\htdocs\sistema_laboratorios\views\docente\gestionar_solicitud.php -->
<?php
session_start();
require_once '../database.php'; // Ruta corregida

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $solicitud_id = $_POST['solicitud_id'];
    $accion = $_POST['accion'];

    $db = new Database();
    $conn = $db->conectar();

    // Obtener los detalles de la solicitud
    $sql = "SELECT * FROM solicitudes WHERE SolicitudID = :solicitud_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
    $stmt->execute();
    $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$solicitud) {
        die("Error: Solicitud no encontrada.");
    }

    if ($accion === 'aprobar') {
        // Validar que los datos necesarios estén presentes en la solicitud
        if (empty($solicitud['HoraInicio']) || empty($solicitud['CantidadHoras'])) {
            die("Error: La solicitud no tiene una hora de inicio o duración válida.");
        }

        $hora_inicio = $solicitud['HoraInicio'];
        $cantidad_horas = $solicitud['CantidadHoras'];
        $hora_fin = date('H:i:s', strtotime($hora_inicio . " + {$cantidad_horas} hours"));

        // Verificar si el horario está disponible
        $sql = "SELECT * FROM reservas WHERE LaboratorioID = :laboratorio_id AND FechaReserva = :fecha
                AND ((HoraInicio <= :hora_inicio AND HoraFin > :hora_inicio) OR (HoraInicio < :hora_fin AND HoraFin >= :hora_fin))";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':laboratorio_id', $solicitud['LaboratorioID'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $solicitud['FechaSolicitud'], PDO::PARAM_STR);
        $stmt->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':hora_fin', $hora_fin, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            die("Error: El horario solicitado ya está ocupado.");
        }

        // Aprobar la solicitud
        $sql = "UPDATE solicitudes SET Estado = 'Aprobada' WHERE SolicitudID = :solicitud_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
        $stmt->execute();

        // Registrar la reserva
        $sql = "INSERT INTO reservas (SolicitudID, LaboratorioID, FechaReserva, HoraInicio, HoraFin)
                VALUES (:solicitud_id, :laboratorio_id, :fecha, :hora_inicio, :hora_fin)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
        $stmt->bindParam(':laboratorio_id', $solicitud['LaboratorioID'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $solicitud['FechaSolicitud'], PDO::PARAM_STR);
        $stmt->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':hora_fin', $hora_fin, PDO::PARAM_STR);
        $stmt->execute();

        // Redirigir de vuelta a detalles_solicitud.php
        header("Location: ../views/docente/detalles_solicitud.php?solicitud_id=$solicitud_id");
        exit;
    } elseif ($accion === 'rechazar') {
        // Rechazar la solicitud
        $sql = "UPDATE solicitudes SET Estado = 'Rechazada' WHERE SolicitudID = :solicitud_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirigir de vuelta a detalles_solicitud.php
        header("Location: ../views/docente/detalles_solicitud.php?solicitud_id=$solicitud_id");
        exit;
    }
}
?>