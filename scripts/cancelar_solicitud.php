<?php
session_start();
require_once '../database.php'; // Ruta corregida

// Verificar si el usuario ha iniciado sesión y es estudiante
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['Rol'] !== 'Estudiante') {
    header("Location: ../login.php");
    exit;
}

// Verificar si se recibió el ID de la solicitud
if (!isset($_GET['solicitud_id'])) {
    die("Solicitud no especificada.");
}

$solicitud_id = $_GET['solicitud_id'];
$usuario_id = $_SESSION['usuario']['UsuarioID'];

$db = new Database();
$conn = $db->conectar();

// Verificar que la solicitud pertenezca al estudiante y esté en estado "Pendiente"
$sql = "SELECT * FROM solicitudes WHERE SolicitudID = :solicitud_id AND UsuarioID = :usuario_id AND Estado = 'Pendiente'";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':solicitud_id', $solicitud_id);
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    die("No se puede cancelar esta solicitud. Verifica que esté en estado 'Pendiente' y que te pertenezca.");
}

// Actualizar el estado de la solicitud a "Cancelada"
$sql = "UPDATE solicitudes SET Estado = 'Cancelada' WHERE SolicitudID = :solicitud_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':solicitud_id', $solicitud_id);

if ($stmt->execute()) {
    echo "Solicitud cancelada correctamente.";
    header("Location: ../views/estudiante/revisar_respuesta.php");
    exit;
} else {
    echo "Error al cancelar la solicitud.";
}
?>