<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['correo'])) {
    http_response_code(401);
    echo json_encode([]);
    exit();
}

// Conexión a la base de datos (mantén tus credenciales)
$host = 'xxxxxxxx';
$db   = 'xxxxxxxx';
$user = 'xxxxxxxx';
$pass = 'xxxxxxxx';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([]);
    exit();
}

// Traer todos los usuarios ordenados por puntaje desc
$sql = "SELECT nom_usuario, foto_perfil, puntaje_global
        FROM alumno
        ORDER BY puntaje_global DESC";
$result = $conn->query($sql);

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
echo json_encode($rows);

$conn->close();
