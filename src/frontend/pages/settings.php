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
        <div class="profile-section">
            <div class="botton-section">
                <div class="profile-pic">
                    <img src="<?php echo isset($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : '../images/default.png'; ?>" class="perfil-imagen">
                    <span class="plus-icon <?php echo isset($_SESSION['foto']) ? 'hidden' : ''; ?> " 
                        onclick="document.getElementById('edit-form').style.display='block';">+</span>
                </div>
                <div class="user-info">
                    <p><?php echo htmlspecialchars($_SESSION['nom_usuario']); ?></p>
                    
                    <span class="edit-icon" onclick="document.getElementById('edit-form').style.display='block';">
                        <img src="../images/lapiz.png" alt="lapiz" class="lapiz">
                    </span>

                    <form id="edit-form" action="../../backend/update_profile.php" method="POST" enctype="multipart/form-data" style="display: none; margin-top: 10px;">
                        <input type="text" name="nuevo_nombre" placeholder="Nuevo nombre" required>
                        <input type="file" name="nueva_foto" accept="image/*">
                        <button type="submit">Guardar cambios</button>
                    </form>
                </div>
            </div>
            <a href="Leaderboard.html">
            <button class="score-button">Leaderboard</button>
            </a>
        </div>

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
            <a href="questions.html">
            <button class="play-button">Jugar</button>
            </a>
        </div>
    </div>
</body>
</html>
