<?php
require_once 'database.php';
$db = new Database();
$conn = $db->conectar();

if ($conn) {
    echo "Conexión exitosa a la base de datos.";
} else {
    echo "Error al conectar a la base de datos.";
}
?>