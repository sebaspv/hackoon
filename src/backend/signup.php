<?php
session_start();

// conectar a DB
$host = 'xxxxxxx';
$db = 'xxxxxxx';
$user = 'xxxxxxx';
$pass = 'xxxxxxx';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "<script>alert('Error de conexión con la base de datos.'); window.history.back();</script>";
    exit();
}

$correo = trim($_POST['correo']);
$usuario = trim($_POST['usuario']);
$contrasena = $_POST['contrasena'];

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Ingresa un correo electrónico válido.'); window.history.back();</script>";
    exit();
}

// Validar pass
if (strlen($contrasena) < 8 || !preg_match('/[\W]/', $contrasena)) {
    echo "<script>alert('La contraseña debe tener al menos 8 caracteres y contener al menos un carácter especial.'); window.history.back();</script>";
    exit();
}

// Checar si correo existe
$stmt = $conn->prepare("SELECT correo FROM alumno WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    echo "<script>alert('Ya existe una cuenta con ese correo.'); window.history.back();</script>";
    exit();
}
$stmt->close();

// METER HASHEADA NO QUEREMOS CONTRASENAS PUBLICAS POR FAVOR XD
$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO alumno (correo, contrasena, nom_usuario, progr_niveles, puntaje_global, foto_perfil) VALUES (?, ?, ?, 0, 0, NULL)");
$stmt->bind_param("sss", $correo, $contrasena_hash, $usuario);

if ($stmt->execute()) {
    $_SESSION['correo'] = $correo;
    $_SESSION['nom_usuario'] = $usuario;
    header("Location: ../frontend/pages/settings.php");
    exit();
} else {
    echo "<script>alert('Error al registrar el usuario. Intenta de nuevo.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
