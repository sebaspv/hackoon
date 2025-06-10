<?php
session_start();

if (!isset($_SESSION['correo'])) {
    header("Location: login.html");
    exit();
}

require_once '../../backend/config.php';

function obtener_frutas_usuario($pdo, $correo_usuario){
    $stmt=$pdo->prepare("
        SELECT c.*
        FROM coleccionable c
        JOIN objeto_usuario ou ON c.id_objeto=ou.id_objeto
        WHERE ou.correo_usuario=?
    ");
    $stmt->execute([$correo_usuario]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$frutas_config = [
    1 => ['nombre' => 'manzana', 'imagen' => '../images/manzana.png', 'tema' => 'Algoritmos'],
    2 => ['nombre' => 'limón', 'imagen' => '../images/limon.png', 'tema' => 'Tipo de Datos'],
    3 => ['nombre' => 'fresa', 'imagen' => '../images/fresa.png', 'tema' => 'Expresiones'],
    4 => ['nombre' => 'pera', 'imagen' => '../images/pera.png', 'tema' => 'Funciones'],
    5 => ['nombre' => 'pina', 'imagen' => '../images/pina.png', 'tema' => 'Condicionales'],
    6 => ['nombre' => 'cereza', 'imagen' => '../images/cereza.png', 'tema' => 'Ciclos'],
    7 => ['nombre' => 'uva', 'imagen' => '../images/uva.png', 'tema' => 'Listas y Matrices'],
    8 => ['nombre' => 'platano', 'imagen' => '../images/platano.png', 'tema' => 'Archivos'],
    9 => ['nombre' => 'sandia', 'imagen' => '../images/sandia.png', 'tema' => 'Maestro']
];

$frutas_desbloqueadas=obtener_frutas_usuario($pdo, $_SESSION['correo']);
$ids_desbloqueadas=array_column($frutas_desbloqueadas, 'id_objeto');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Usuario</title>
    <link rel="stylesheet" href="../css/settings.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <div class="profile-section">
            <div class="botton-section">
                <div class="profile-pic">
                    <img src="<?php echo isset($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : '../images/default.png'; ?>" class="perfil-imagen" id="preview">
                    <span class="plus-icon <?php echo isset($_SESSION['foto']) ? 'hidden' : ''; ?>" onclick="document.getElementById('edit-form').style.display='flex';">+</span>
                </div>

                <div class="user-info">
                    <p><?php echo htmlspecialchars($_SESSION['nom_usuario']); ?></p>
                    <span class="edit-icon" onclick="document.getElementById('edit-form').style.display='flex';">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="22" height="22" fill="white">
                            <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1 0 32c0 8.8 7.2 16 16 16l32 0zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/>
                        </svg>
                    </span>
                </div>
            </div>

            <form id="edit-form" action="../../backend/update_profile.php" method="POST" enctype="multipart/form-data" class="form-controls" style="display: none;">
                <input type="text" name="nuevo_nombre" placeholder="Nuevo nombre" required>
                <label for="nueva_foto" class="custom-file-upload">Seleccionar imagen</label>
                <input type="file" id="nueva_foto" name="nueva_foto" class="hidden" accept="image/*" onchange="previewImage(event)">

                <input type="submit" value="Guardar cambios">
            </form>

            <a href="leaderboard.html"><button class="score-button">Leaderboard</button></a>
        </div>

        <div class="collections-section">
            <h2>Colecciones</h2>
            <div class="collections-grid">
                <?php
                for ($fila=0; $fila<3; $fila++){
                    echo '<div class="collection-row">';
                    for ($col=1; $col<=3; $col++){
                        $indice=($fila*3)+$col;
                        $desbloqueada=in_array($indice, $ids_desbloqueadas);
                        $es_especial=($indice==9);

                        if($desbloqueada){
                            $clase_extra=$es_especial?'unlocked especial':'unlocked';
                            $imagen=$frutas_config[$indice]['imagen'];
                            $tooltip=$frutas_config[$indice]['nombre'] . ' - ' . $frutas_config[$indice]['tema'];
                        } else{
                            $clase_extra='locked';
                            $imagen='../images/lock.png';
                            $tooltip=$es_especial?'sandia-Completa todos los temas':'Completa el tema: ' . $frutas_config[$indice]['tema'];                      
                        }

                        echo '<div class="collection-item ' . $clase_extra . '" title="' . htmlspecialchars($tooltip) . '">';
                        echo '<img src="' . $imagen . '"alt="' . ($desbloqueada ? $frutas_config[$indice]['nombre']: 'bloqueado') . '">';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
            <a href="questions.html"><button class="play-button">Jugar</button></a>

            <div style="margin-top: 1rem; text-align: center; color: #ccc; font-size: 0.9rem;">
                Frutas desbloqueadas: <?php echo count($frutas_desbloqueadas); ?>/9
            </div>
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