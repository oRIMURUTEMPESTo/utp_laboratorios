<!-- filepath: c:\xampp\htdocs\sistema_laboratorios\scripts\api_calendario_general.php -->
<?php
require_once '../database.php';

$db = new Database();
$conn = $db->conectar();

// Consulta para obtener eventos de todos los laboratorios
$sql = "
    SELECT 
        r.ReservaID AS id,
        CONCAT('Reserva: ', l.Nombre) AS title,
        CONCAT(r.FechaReserva, 'T', r.HoraInicio) AS start,
        CONCAT(r.FechaReserva, 'T', r.HoraFin) AS end,
        'reservado' AS estado
    FROM reservas r
    JOIN laboratorios l ON r.LaboratorioID = l.LaboratorioID
    UNION
    SELECT 
        s.SolicitudID AS id,
        CONCAT('Solicitud: ', l.Nombre) AS title,
        CONCAT(s.FechaSolicitud, 'T', s.HoraInicio) AS start,
        CONCAT(s.FechaSolicitud, 'T', ADDTIME(s.HoraInicio, SEC_TO_TIME(s.CantidadHoras * 3600))) AS end,
        'pendiente' AS estado
    FROM solicitudes s
    JOIN laboratorios l ON s.LaboratorioID = l.LaboratorioID
    WHERE s.Estado = 'Pendiente'
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($eventos);
?>