<?php
require 'vendor/autoload.php'; // Ajusta la ruta si es necesario
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

session_start();

$host = 'xxxxxxxx';
$db = 'xxxxxxxx';
$user = 'xxxxxxxx';
$pass = 'xxxxxxxx';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "<script>alert('Error de conexión con la base de datos.'); window.history.back();</script>";
    exit();
}

$correo = $_SESSION['correo'];
$nuevo_nombre = $_POST['nuevo_nombre'];
$url_imagen = null;

// Parámetros de Cellar S3
$bucketName = 'xxxxxxxx';
$region = 'xxxxxxxx';
$endpoint = 'xxxxxxxx';
$key = 'xxxxxxxx';
$secret = 'xxxxxxxx';

// Crear cliente S3
$s3 = new S3Client([
    'version' => 'latest',
    'region' => $region,
    'endpoint' => $endpoint,
    'credentials' => [
        'key' => $key,
        'secret' => $secret,
    ],
]);

// Subir a S3 si se subió imagen
if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] == 0) {
    $nombre_archivo = time() . "_" . basename($_FILES['nueva_foto']['name']);
    $ruta_temporal = $_FILES['nueva_foto']['tmp_name'];

    try {
        $resultado = $s3->putObject([
            'Bucket' => $bucketName,
            'Key' => $nombre_archivo,
            'SourceFile' => $ruta_temporal,
            'ACL' => 'public-read',
        ]);
        $url_imagen = $resultado['ObjectURL'];
    } catch (AwsException $e) {
        die("Error al subir imagen a S3: " . $e->getMessage());
    }
}

// Armar query
if ($url_imagen) {
    $sql = "UPDATE alumno SET nom_usuario = ?, foto_perfil = ? WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nuevo_nombre, $url_imagen, $correo);
} else {
    $sql = "UPDATE alumno SET nom_usuario = ? WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nuevo_nombre, $correo);
}

if ($stmt->execute()) {
    $_SESSION['nom_usuario'] = $nuevo_nombre;
    if ($url_imagen) {
        $_SESSION['foto'] = $url_imagen;
    }
    header("Location: ../frontend/pages/settings.php");
    exit();
} else {
    echo "Error al actualizar perfil.";
}

$stmt->close();
$conn->close();
