<?php
session_start();

if (!isset($_SESSION['correo'])) {
    echo json_encode(["error" => "Sesión no iniciada."]);
    exit();
}

$correo_usuario = $_SESSION['correo'];

$host = 'XXXXXXXXXX';
$db = 'XXXXXXXXXX';
$user = 'XXXXXXXXXX';
$pass = 'XXXXXXXXXX';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["error" => "Conexión fallida."]);
    exit();
}

header("Content-Type: application/json; charset=UTF-8");

$input = json_decode(file_get_contents("php://input"), true);
$id_pregunta       = $input["id_pregunta"]       ?? '';
$respuesta_usuario = strtolower(trim($input["respuesta"] ?? ''));

if (!$id_pregunta || !$respuesta_usuario) {
    echo json_encode(["error" => "Faltan datos."]);
    exit();
}

$sql = "SELECT respuesta_correcta, dificultad FROM pregunta WHERE id_pregunta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pregunta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Pregunta no encontrada."]);
    exit();
}

$row = $result->fetch_assoc();
$respuesta_correcta = strtolower(trim($row["respuesta_correcta"]));
$dificultad         = (int)$row["dificultad"];

$es_correcta = ($respuesta_usuario === $respuesta_correcta);

$puntos = 0;
if ($es_correcta) {
    switch ($dificultad) {
        case 1: $puntos = 10; break;
        case 2: $puntos = 15; break;
        case 3: $puntos = 20; break;
    }

    $stmt = $conn->prepare("INSERT INTO usuario_nivel (correo_usuario, id_pregunta, puntaje_obtenido)
                            VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE puntaje_obtenido = GREATEST(puntaje_obtenido, ?)");
    $stmt->bind_param("siii", $correo_usuario, $id_pregunta, $puntos, $puntos);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE alumno SET puntaje_global = puntaje_global + ? WHERE correo = ?");
    $stmt->bind_param("is", $puntos, $correo_usuario);
    $stmt->execute();
}

echo json_encode([
    "correcto" => $es_correcta,
    "puntos_asignados" => $puntos
]);
?>
