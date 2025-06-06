<?php
$host = 'xxxxxxxx';
$db   = 'xxxxxxxx';
$user = 'xxxxxxxx';
$pass = 'xxxxxxxx';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$tema = isset($_GET['tema']) ? $_GET['tema'] : '';
$dificultad = isset($_GET['dificultad']) ? $_GET['dificultad'] : '';

if (empty($tema) && empty($dificultad)) {
$sql = "
    (SELECT p.id_pregunta, p.nombre_pregunta, p.pregunta, d.dificultad AS nombre_dificultad, t.tema AS nombre_tema
     FROM pregunta p
     JOIN dificultad d ON p.dificultad = d.id_dificultad
     JOIN pregunta_tema pt ON p.id_pregunta = pt.id_pregunta
     JOIN tema t ON pt.id_tema = t.id_tema
     WHERE d.dificultad = 'Fácil'
     ORDER BY RAND()
     LIMIT 3)
    UNION
    (SELECT p.id_pregunta, p.nombre_pregunta, p.pregunta, d.dificultad AS nombre_dificultad, t.tema AS nombre_tema
     FROM pregunta p
     JOIN dificultad d ON p.dificultad = d.id_dificultad
     JOIN pregunta_tema pt ON p.id_pregunta = pt.id_pregunta
     JOIN tema t ON pt.id_tema = t.id_tema
     WHERE d.dificultad = 'Intermedio'
     ORDER BY RAND()
     LIMIT 2)
    UNION
    (SELECT p.id_pregunta, p.nombre_pregunta, p.pregunta, d.dificultad AS nombre_dificultad, t.tema AS nombre_tema
     FROM pregunta p
     JOIN dificultad d ON p.dificultad = d.id_dificultad
     JOIN pregunta_tema pt ON p.id_pregunta = pt.id_pregunta
     JOIN tema t ON pt.id_tema = t.id_tema
     WHERE d.dificultad = 'Difícil'
     ORDER BY RAND()
     LIMIT 1)";

} else {
    $sql = "
    SELECT p.id_pregunta, p.nombre_pregunta, p.pregunta, d.dificultad AS nombre_dificultad, t.tema AS nombre_tema
    FROM pregunta p
    JOIN dificultad d ON p.dificultad = d.id_dificultad
    JOIN pregunta_tema pt ON p.id_pregunta = pt.id_pregunta
    JOIN tema t ON pt.id_tema = t.id_tema
    WHERE 1=1";

    if (!empty($tema)) {
        $sql .= " AND t.tema = '" . $conn->real_escape_string($tema) . "'";
    }
    if (!empty($dificultad)) {
        $sql .= " AND d.dificultad = '" . $conn->real_escape_string($dificultad) . "'";
    }

    //$sql .= " LIMIT 6";
}

$result = $conn->query($sql);

$preguntas = [];

while ($row = $result->fetch_assoc()) {
    $preguntas[] = $row;
}

echo json_encode($preguntas, JSON_UNESCAPED_UNICODE);
$conn->close();
?>
