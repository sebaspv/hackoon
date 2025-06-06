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
$respuesta_correcta = $input['respuesta_correcta'] ?? false;

if (!$id_pregunta) {
    echo json_encode(['error' => 'ID de pregunta requerido']);
    exit();
}

require_once 'config.php';

try {
    $correo_usuario = $_SESSION['correo'];

    if ($respuesta_correcta) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario_nivel WHERE correo_usuario = ? AND id_pregunta = ?");
        $stmt->execute([$correo_usuario, $id_pregunta]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            $stmt = $pdo->prepare("INSERT INTO usuario_nivel (correo_usuario, id_pregunta, puntaje_obtenido) VALUES (?, ?, 100)");
            if (!$stmt->execute([$correo_usuario, $id_pregunta])) {
                error_log("No se insertó en usuario_nivel: " . implode(" | ", $stmt->errorInfo()));
            }

            $tema_completado = verificar_tema_completado($pdo, $correo_usuario, $id_pregunta);

            // Contar frutas después de intentar desbloquear
            $total_frutas = contar_frutas_desbloqueadas($pdo, $correo_usuario);

            if ($tema_completado) {
                desbloquear_fruta_tema($pdo, $correo_usuario, $tema_completado);
            }

            if ($total_frutas == 7) {
                desbloquear_fruta_especial($pdo, $correo_usuario);
                $total_frutas++; // Contamos también la fruta especial
            }

            if ($tema_completado) {
                echo json_encode([
                    'success' => true,
                    'mensaje' => 'Progreso guardado y fruta desbloqueada!',
                    'tema_completado' => $tema_completado['tema'],
                    'total_frutas' => $total_frutas
                ]);
            } else {
                echo json_encode(['success' => true, 'mensaje' => 'Progreso guardado']);
            }
        } else {
            echo json_encode(['success' => true, 'mensaje' => 'Pregunta ya completada anteriormente']);
        }
    } else {
        echo json_encode(['success' => true, 'mensaje' => 'Respuesta incorrecta']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al guardar progreso: ' . $e->getMessage()]);
}

function verificar_tema_completado($pdo, $correo_usuario, $id_pregunta) {
    $stmt = $pdo->prepare("
        SELECT tema.id_tema, tema.tema 
        FROM tema 
        JOIN pregunta_tema pregunta_tema ON tema.id_tema = pregunta_tema.id_tema 
        WHERE pregunta_tema.id_pregunta = ?
    ");
    $stmt->execute([$id_pregunta]);
    $tema = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tema) return false;

    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM pregunta_tema 
        WHERE id_tema = ?
    ");
    $stmt->execute([$tema['id_tema']]);
    $total_preguntas = $stmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT COUNT(*) as completadas 
        FROM usuario_nivel un 
        JOIN pregunta_tema pt ON un.id_pregunta = pt.id_pregunta 
        WHERE un.correo_usuario = ? AND pt.id_tema = ?
    ");
    $stmt->execute([$correo_usuario, $tema['id_tema']]);
    $completadas = $stmt->fetchColumn();

    error_log("Tema {$tema['id_tema']}: {$completadas}/{$total_preguntas} preguntas completadas");

    return ($completadas >= $total_preguntas) ? $tema : false;
}

function desbloquear_fruta_tema($pdo, $correo_usuario, $tema) {
    $frutas_por_tema = [
        1 => 1, 2 => 2, 3 => 3, 4 => 4,
        5 => 5, 6 => 6, 7 => 7, 8 => 8
    ];

    $id_fruta = $frutas_por_tema[$tema['id_tema']] ?? null;

    if ($id_fruta) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM objeto_usuario WHERE correo_usuario = ? AND id_objeto = ?");
        $stmt->execute([$correo_usuario, $id_fruta]);
        $tiene_fruta = $stmt->fetchColumn();

        if (!$tiene_fruta) {
            $stmt = $pdo->prepare("INSERT INTO objeto_usuario (correo_usuario, id_objeto) VALUES (?, ?)");
            $stmt->execute([$correo_usuario, $id_fruta]);
            error_log("Fruta {$id_fruta} desbloqueada para usuario {$correo_usuario}");
        }
    }
}

function desbloquear_fruta_especial($pdo, $correo_usuario) {
    $id_fruta_especial = 9;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM objeto_usuario WHERE correo_usuario = ? AND id_objeto = ?");
    $stmt->execute([$correo_usuario, $id_fruta_especial]);
    $tiene_fruta = $stmt->fetchColumn();

    if (!$tiene_fruta) {
        $stmt = $pdo->prepare("INSERT INTO objeto_usuario (correo_usuario, id_objeto) VALUES (?, ?)");
        $stmt->execute([$correo_usuario, $id_fruta_especial]);
        error_log("Fruta especial desbloqueada para usuario {$correo_usuario}");
    }
}

function contar_frutas_desbloqueadas($pdo, $correo_usuario) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM objeto_usuario 
        WHERE correo_usuario = ? AND id_objeto BETWEEN 1 AND 8
    ");
    $stmt->execute([$correo_usuario]);
    return $stmt->fetchColumn();
}
?>