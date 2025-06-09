<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['correo'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$id_pregunta = $input['id_pregunta'] ?? null;
$respuesta_usuario = strtolower(trim($input['respuesta'] ?? ''));

if (!$id_pregunta || !$respuesta_usuario) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit();
}

require_once 'config.php'; // Aquí debes tener $pdo definido
$correo = $_SESSION['correo'];

// 1. Obtener respuesta correcta y dificultad
$stmt = $pdo->prepare("SELECT respuesta_correcta, dificultad FROM pregunta WHERE id_pregunta = ?");
$stmt->execute([$id_pregunta]);
$pregunta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pregunta) {
    echo json_encode(['error' => 'Pregunta no encontrada']);
    exit();
}

$respuesta_correcta = strtolower(trim($pregunta['respuesta_correcta']));
$es_correcta = ($respuesta_usuario === $respuesta_correcta);

// 2. Calcular puntos
$puntos = 0;
if ($es_correcta) {
    switch ((int)$pregunta['dificultad']) {
        case 1: $puntos = 10; break;
        case 2: $puntos = 15; break;
        case 3: $puntos = 20; break;
    }

    // 3. Insertar/actualizar puntuación
    $stmt = $pdo->prepare("INSERT INTO usuario_nivel (correo_usuario, id_pregunta, puntaje_obtenido)
                           VALUES (?, ?, ?)
                           ON DUPLICATE KEY UPDATE puntaje_obtenido = GREATEST(puntaje_obtenido, ?)");
    $stmt->execute([$correo, $id_pregunta, $puntos, $puntos]);

    $stmt = $pdo->prepare("UPDATE alumno SET puntaje_global = puntaje_global + ? WHERE correo = ?");
    $stmt->execute([$puntos, $correo]);

    // 4. Verificar colecciones
    $coleccion = desbloquear_objetos_si_aplica($pdo, $correo, $id_pregunta);

    echo json_encode([
        "correcto" => true,
        "puntos_asignados" => $puntos,
        "coleccion" => $coleccion
    ]);
} else {
    echo json_encode([
        "correcto" => false,
        "puntos_asignados" => 0
    ]);
}

// Funciones auxiliares ==============================

function desbloquear_objetos_si_aplica($pdo, $correo_usuario, $id_pregunta) {
    $tema = obtener_tema($pdo, $id_pregunta);
    if (!$tema) return null;

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pregunta_tema WHERE id_tema = ?");
    $stmt->execute([$tema['id_tema']]);
    $total = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario_nivel un JOIN pregunta_tema pt 
                           ON un.id_pregunta = pt.id_pregunta 
                           WHERE pt.id_tema = ? AND un.correo_usuario = ?");
    $stmt->execute([$tema['id_tema'], $correo_usuario]);
    $completadas = $stmt->fetchColumn();

    if ($completadas >= $total) {
        // Desbloquea fruta del tema si no la tiene
        $id_objeto = $tema['id_tema']; // asumiendo que objeto y tema tienen mismo ID
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM objeto_usuario WHERE correo_usuario = ? AND id_objeto = ?");
        $stmt->execute([$correo_usuario, $id_objeto]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO objeto_usuario (correo_usuario, id_objeto) VALUES (?, ?)");
            $stmt->execute([$correo_usuario, $id_objeto]);
        }
    }

    // Verificar si ya desbloqueó todas las frutas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM objeto_usuario WHERE correo_usuario = ? AND id_objeto BETWEEN 1 AND 8");
    $stmt->execute([$correo_usuario]);
    $total_frutas = $stmt->fetchColumn();

    if ($total_frutas == 8) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM objeto_usuario WHERE correo_usuario = ? AND id_objeto = 9");
        $stmt->execute([$correo_usuario]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO objeto_usuario (correo_usuario, id_objeto) VALUES (?, 9)");
            $stmt->execute([$correo_usuario]);
        }
    }

    return true;
}

function obtener_tema($pdo, $id_pregunta) {
    $stmt = $pdo->prepare("SELECT tema.id_tema, tema.tema FROM tema
                           JOIN pregunta_tema pt ON tema.id_tema = pt.id_tema
                           WHERE pt.id_pregunta = ?");
    $stmt->execute([$id_pregunta]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
