<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'models/Reserva.php';

$reservaModel = new Reserva();
// ID of user who booked (maybe 3 since we just registered)
// We can get all users
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query("SELECT id FROM usuarios ORDER BY id DESC LIMIT 1");
$usuario_id = $stmt->fetchColumn();

echo "Testing panel for usuario_id: $usuario_id\n";

try {
    $misReservas = $reservaModel->obtenerReservasUsuario($usuario_id);
    print_r($misReservas);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
