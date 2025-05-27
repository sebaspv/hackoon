<?php
$host = "XXXXXXXXXX";
$user = "XXXXXXXXXX";
$pass = "XXXXXXXXXX";
$db = "XXXXXXXXXX";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Obtener ID de la pregunta
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) {
    echo json_encode(["error" => "ID no válido"]);
    exit;
}

// Consulta de la pregunta por ID
$sql = "
SELECT 
    p.id_pregunta,
    p.nombre_pregunta,
    p.pregunta,
    p.respuesta1,
    p.respuesta2,
    p.respuesta3,
    p.respuesta_correcta,
    d.dificultad AS nombre_dificultad,
    t.tema AS nombre_tema
FROM pregunta p
JOIN dificultad d ON p.dificultad = d.id_dificultad
JOIN pregunta_tema pt ON p.id_pregunta = pt.id_pregunta
JOIN tema t ON pt.id_tema = t.id_tema
WHERE p.id_pregunta = $id
";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Pregunta no encontrada"]);
    exit;
}

$data = $result->fetch_assoc();
echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conn->close();
?>
