<?php
include '../layout/header.php';
include '../db.php';

$successMessage = "";
$errorMessage = "";
$playerId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $last_name = $_POST['last_name'];
    $document = $_POST['document'];
    $phone = $_POST['phone'];
    $init_point = $_POST['init_point'];
    $localidad = $_POST['localidad'];

    $checkSql = "SELECT id FROM players WHERE document = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $document);
    $checkStmt->execute();
    $checkStmt->store_result();


    if ($checkStmt->num_rows > 0) {
        $errorMessage = "Ya existe un jugador registrado con el DNI $document.";
    } else {
        // Insertar el nuevo jugador en la base de datos
        $sql = "INSERT INTO players (name, last_name, document, phone, points,localidad) 
                VALUES (?, ?, ?, ?, ?, ?)"; 

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $last_name, $document, $phone, $init_point, $localidad); 

        if ($stmt->execute()) {
            // Obtener el ID del jugador insertado
            $playerId = $stmt->insert_id;

            // Generar el mensaje de éxito
            $successMessage = "Jugador registrado exitosamente.";
        } else {
            // Si falla la inserción
            $successMessage = "Hubo un error al registrar al jugador. Inténtelo nuevamente.";
        }
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
                    <label for="localidad">Localidad:</label>
                    <input type="text" name="localidad" required>
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
                <p><?php echo $successMessage; ?></p>
                <?php if ($playerId): ?>
                    <a href="player_profile.php?id=<?php echo $playerId; ?>" class="btn">Ver Perfil del Jugador</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="message error">
                <p><?php echo $errorMessage; ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../layout/footer.php'; ?>