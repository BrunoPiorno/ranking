<!-- <?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'layout/header.php';
include 'db.php';

// Inicializar mensaje de éxito
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $name = $_POST['name'];
    $last_name = $_POST['last_name'];
    //$birthdate = $_POST['birthdate'];
    $document = $_POST['document'];
  //  $email = $_POST['email'];
    $phone = $_POST['phone'];
    $init_point = $_POST['init_point'];

    // Insertar el nuevo jugador en la base de datos
    $sql = "INSERT INTO players (name, last_name, document, phone, points) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $name, $last_name, $document, $phone, $init_point);

    if ($stmt->execute()) {
        // Obtener el ID del jugador insertado
        $player_id = $stmt->insert_id;
        
        // Generar el mensaje de éxito
        $successMessage = "Jugador registrado con éxito. <a href='profile.php?id=$player_id' class='btn'>Ver Perfil</a>";
    } else {
        // Si falla la inserción
        $successMessage = "Hubo un error al registrar al jugador. Inténtelo nuevamente.";
    }
}

?>

<section class="add_player">
    <div class="container">
        <h1 class="title">Registrar Jugador</h1>

        <form class="form" action="add_player.php" method="post">
            <div class="add_player__item">
                <div class="item">
                    <label for="name">Nombre:</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="item">
                    <label for="last_name">Apellido:</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>

            <div class="add_player__item">

                <div class="item">
                    <label for="document">Documento (DNI):</label>
                    <input type="text" name="document" required>
                </div>

                <div class="item">
                    <label for="phone">Teléfono:</label>
                    <input type="tel" name="phone">
                </div>
            </div> 

            <div class="add_player__item">
                <div class="item">
                    <label for="init_point">Puntaje Inicial:</label>
                    <input type="number" name="init_point" required>
                </div>
            </div>
            
            <input type="submit" value="Registrar Jugador">
        </form>

        <?php if ($successMessage): ?>
            <div class="message">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'layout/footer.php'; ?> -->
