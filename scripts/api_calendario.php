<?php
require_once '../database.php';

$db = new Database();
$conn = $db->conectar();

// Verificar si se recibió el ID del laboratorio
if (!isset($_GET['laboratorio_id']) || empty($_GET['laboratorio_id'])) {
    die(json_encode(['error' => 'Laboratorio no especificado']));
}

$laboratorio_id = $_GET['laboratorio_id'];

// Consulta para obtener eventos del calendario
$sql = "
    SELECT 
        r.ReservaID AS id,
        CONCAT('Reserva: ', l.Nombre) AS title,
        CONCAT(r.FechaReserva, 'T', r.HoraInicio) AS start,
        CONCAT(r.FechaReserva, 'T', r.HoraFin) AS end,
        'reservado' AS estado
    FROM reservas r
    JOIN laboratorios l ON r.LaboratorioID = l.LaboratorioID
    WHERE r.LaboratorioID = :laboratorio_id
    UNION
    SELECT 
        s.SolicitudID AS id,
        CONCAT('Solicitud: ', l.Nombre) AS title,
        CONCAT(s.FechaSolicitud, 'T', s.HoraInicio) AS start,
        CONCAT(s.FechaSolicitud, 'T', ADDTIME(s.HoraInicio, SEC_TO_TIME(s.CantidadHoras * 3600))) AS end,
        'pendiente' AS estado
    FROM solicitudes s
    JOIN laboratorios l ON s.LaboratorioID = l.LaboratorioID
    WHERE s.LaboratorioID = :laboratorio_id AND s.Estado = 'Pendiente'
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':laboratorio_id', $laboratorio_id, PDO::PARAM_INT);
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($eventos);
?>