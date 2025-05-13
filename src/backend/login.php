<?php
// destrozar sesion si habia una activa
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

session_start();

$host = 'xxxxxxx';
$db = 'xxxxxxx';
$user = 'xxxxxxx';
$pass = 'xxxxxxx';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "<script>alert('Error de conexión con la base de datos.'); window.history.back();</script>";
    exit();
}

$usuario_input = trim($_POST['usuario']);
$contrasena = $_POST['contrasena'];

$stmt = $conn->prepare("SELECT correo, contrasena, nom_usuario FROM alumno WHERE correo = ? OR nom_usuario = ?");
$stmt->bind_param("ss", $usuario_input, $usuario_input);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($contrasena, $row['contrasena'])) {
        $_SESSION['correo'] = $row['correo']; // VARIABLES DE SESION !!!!!!!!!!!!
        $_SESSION['nom_usuario'] = $row['nom_usuario'];

        header("Location: ../frontend/pages/settings.php");
        exit();
    } else {
        echo "<script>alert('Contraseña incorrecta.'); window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('Correo no encontrado.'); window.history.back();</script>";
    exit();
}

$stmt->close();
$conn->close();
?>
