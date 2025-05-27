<?php
session_start();

if (!isset($_SESSION['correo'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Usuario</title>
    <link rel="stylesheet" href="../css/settings.css">
</head>
<body>
    <div class="container">
        <!-- Lado izquierdo: perfil -->
        <div class="profile-section">
            <div class="botton-section">
                <!-- Imagen de perfil -->
                <div class="profile-pic">
                    <img src="<?php echo isset($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : '../images/default.png'; ?>" class="perfil-imagen" id="preview">
                    <span class="plus-icon <?php echo isset($_SESSION['foto']) ? 'hidden' : ''; ?>" onclick="document.getElementById('edit-form').style.display='flex';">+</span>
                </div>

                <!-- Nombre del usuario -->
                <div class="user-info">
                    <p><?php echo htmlspecialchars($_SESSION['nom_usuario']); ?></p>
                    <span class="edit-icon" onclick="document.getElementById('edit-form').style.display='flex';">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="22" height="22" fill="white">
                            <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1 0 32c0 8.8 7.2 16 16 16l32 0zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Formulario de edición -->
            <form id="edit-form" action="../../backend/update_profile.php" method="POST" enctype="multipart/form-data" class="form-controls" style="display: none;">
                <input type="text" name="nuevo_nombre" placeholder="Nuevo nombre" required>

                <!-- Botón estilizado para imagen -->
                <label for="nueva_foto" class="custom-file-upload">Seleccionar imagen</label>
                <input type="file" id="nueva_foto" name="nueva_foto" class="hidden" accept="image/*" onchange="previewImage(event)">

                <input type="submit" value="Guardar cambios">
            </form>

            <a href="Leaderboard.html"><button class="score-button">Leaderboard</button></a>
        </div>

        <!-- Lado derecho: colecciones -->
        <div class="collections-section">
            <h2>Colecciones</h2>
            <div class="collections-grid">
                <div class="collection-row">
                    <div class="collection-item"><img src="../images/apple.png" alt="manzana"></div>
                    <div class="collection-item"><img src="../images/lemon.png" alt="limón"></div>
                    <div class="collection-item"><img src="../images/carrot.png" alt="zanahoria"></div>
                </div>
                <div class="collection-row">
                    <div class="collection-item locked"><img src="../images/lock.png" alt="bloqueado"></div>
                    <div class="collection-item locked"><img src="../images/lock.png" alt="bloqueado"></div>
                    <div class="collection-item locked"><img src="../images/lock.png" alt="bloqueado"></div>
                </div>
                <div class="collection-row">
                    <div class="collection-item locked"><img src="../images/lock.png" alt="bloqueado"></div>
                    <div class="collection-item locked"><img src="../images/lock.png" alt="bloqueado"></div>
                    <div class="collection-item locked"><img src="../images/lock.png" alt="bloqueado"></div>
                </div>
            </div>
            <a href="questions.html"><button class="play-button">Jugar</button></a>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('preview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
