<?php
session_start();
include 'layout/header.php';
include 'db.php';

$successMessage = "";
$tournament_details = null;
$tournament_results = [];
$tournaments = [];

// Obtener la lista de torneos
$sql_tournaments = "SELECT * FROM tournaments";
$result_tournaments = $conn->query($sql_tournaments);
while ($tournament = $result_tournaments->fetch_assoc()) {
    $tournaments[] = $tournament;
}

// Obtener la lista de jugadores
$sql_players = "SELECT id, name, last_name FROM players";
$result_players = $conn->query($sql_players);
$players = [];
while ($row = $result_players->fetch_assoc()) {
    $players[] = $row;
}

// Agregar torneo
if (isset($_POST['add_tournament'])) {
    $tournament_name = $_POST['tournament_name'];
    $tournament_date = $_POST['tournament_date'];
    $category = $_POST['category'];

    $sql_insert_tournament = "INSERT INTO tournaments (name, tournament_date, category) VALUES (?, ?, ?)";
    $stmt_insert_tournament = $conn->prepare($sql_insert_tournament);
    $stmt_insert_tournament->bind_param("sss", $tournament_name, $tournament_date, $category);
    
    if ($stmt_insert_tournament->execute()) {
        $successMessage = "Torneo agregado con éxito.";
    } else {
        $successMessage = "Hubo un error al agregar el torneo.";
    }
}

if (isset($_GET['id'])) {
    $tournament_id = $_GET['id'];

    $sql_tournament = "SELECT name FROM tournaments WHERE id = ?";
    $stmt_tournament = $conn->prepare($sql_tournament);
    $stmt_tournament->bind_param("i", $tournament_id);
    $stmt_tournament->execute();
    $result_tournament = $stmt_tournament->get_result();
    $tournament_details = $result_tournament->fetch_assoc();

    $sql_results = "SELECT r.position, r.category, p.name, p.last_name
                    FROM results r
                    JOIN players p ON r.player_id = p.id
                    WHERE r.tournament_id = ?
                    ORDER BY r.category, r.position ASC";
    $stmt_results = $conn->prepare($sql_results);
    $stmt_results->bind_param("i", $tournament_id);
    $stmt_results->execute();
    $tournament_results = $stmt_results->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Agregar resultados de torneo
// Después de agregar los resultados del torneo, actualizar los puntos de los jugadores
if (isset($_POST['add_result']) && isset($_POST['tournament_id'])) {
    $tournament_id = $_POST['tournament_id'];
    $category = $_POST['category']; // Nueva categoría seleccionada
    $players = [
        $_POST['player1'],
        $_POST['player2'],
        $_POST['player3'],
        $_POST['player4']
    ];

    // Construir la consulta SQL para insertar todos los resultados de una vez
    $sql_insert_results = "INSERT INTO results (tournament_id, player_id, position, category) VALUES ";
    $values = [];
    $types = "";  // Inicializamos los tipos vacíos

    foreach ($players as $position => $player) {
        // Para cada jugador, agregamos un conjunto de valores de los parámetros
        $values[] = "(?, ?, ?, ?)";
        // Añadir los tipos correspondientes para cada conjunto de parámetros
        $types .= "iiss";  // Agregamos 'iiss' para cada jugador
    }

    // Concatenar todos los valores
    $sql_insert_results .= implode(", ", $values);

    // Preparar la sentencia
    $stmt_insert = $conn->prepare($sql_insert_results);

    // Crear un array para los parámetros
    $params = [];
    foreach ($players as $position => $player) {
        $params[] = $tournament_id;
        $params[] = $player;
        $params[] = $position + 1; // La posición se establece como 1, 2, 3, etc.
        $params[] = $category;
    }

    // Unir los parámetros para el bind_param
    $stmt_insert->bind_param($types, ...$params);

    // Ejecutar la sentencia
    if ($stmt_insert->execute()) {
        $successMessage = "Resultados agregados con éxito.";

        // Actualizar los puntos de los jugadores según su posición
        foreach ($players as $position => $player) {
            $points = calculate_points($position + 1); // Calcular puntos basados en la posición
            $stmt_update_points = $conn->prepare("UPDATE players SET points = IFNULL(points, 0) + ? WHERE id = ?");
            $stmt_update_points->bind_param("ii", $points, $player);
            $stmt_update_points->execute();
        }
    } else {
        // Capturar el error en caso de fallo
        $errorMessage = "Hubo un error al agregar los resultados: " . $stmt_insert->error;
    }

    // Mostrar mensaje de éxito o error
    if (isset($successMessage)) {
        echo $successMessage;
    } elseif (isset($errorMessage)) {
        echo $errorMessage;
    }
}


// Calculamos los puntos de acuerdo a la posición
function calculate_points($position) {
    switch ($position) {
        case 1:
            $bonus_points = 30;
            break;
        case 2:
            $bonus_points = 25;
            break;
        case 3:
            $bonus_points = 21;
            break;
        case 4:
            $bonus_points = 17;
            break;
        default:
            $bonus_points = 0;
            break;
    }

    return $bonus_points;
}


?>

<section class="torneos">
    <div class="container">
    <?php if (isset($_SESSION['user_id'])): ?>
        <h1>Registrar Torneo</h1>

        <?php if ($successMessage): ?>
            <div class="message">
                <p><?php echo $successMessage; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="tournament_name">Nombre del Torneo:</label>
            <input type="text" name="tournament_name" required>

            <label for="tournament_date">Fecha del Torneo:</label>
            <input type="date" name="tournament_date" required>

            <input type="submit" name="add_tournament" value="Registrar Torneo">
        </form>

        <h1>Cargar Resultados del Torneo</h1>

        <form method="POST">
            <h2>Agregar Resultados</h2>

            <label for="tournament_id">Torneo:</label>
            <select name="tournament_id" required>
                <option value="">Seleccione un torneo</option>
                <?php foreach ($tournaments as $tournament): ?>
                    <option value="<?php echo $tournament['id']; ?>">
                        <?php echo $tournament['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="category">Categoría:</label>
            <select name="category" required>
                <option value="menores">Menores</option>
                <option value="cuarta">Cuarta</option>
                <option value="tercera">Tercera</option>
                <option value="segunda">Segunda</option>
                <option value="primera">Primera</option>
            </select>

            <div class="players_cont">
                <div class="player">
                    <label for="player1">1er Puesto (Jugador):</label>
                    <select name="player1" required>
                        <?php foreach ($players as $player): ?>
                            <option value="<?php echo $player['id']; ?>">
                            <?php echo $player['last_name'] . ' ' . $player['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="player">
                    <label for="player2">2do Puesto (Jugador):</label>
                    <select name="player2" required>
                        <?php foreach ($players as $player): ?>
                            <option value="<?php echo $player['id']; ?>">
                                <?php echo $player['last_name'] . ' ' . $player['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="player">
                    <label for="player3">3er Puesto (Jugador):</label>
                    <select name="player3" required>
                        <?php foreach ($players as $player): ?>
                            <option value="<?php echo $player['id']; ?>">
                            <?php echo $player['last_name'] . ' ' . $player['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="player">
                    <label for="player4">4to Puesto (Jugador):</label>
                    <select name="player4" required>
                        <?php foreach ($players as $player): ?>
                            <option value="<?php echo $player['id']; ?>">
                            <?php echo $player['last_name'] . ' ' . $player['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            

            <input type="submit" name="add_result" value="Registrar Resultados">
        </form>
    <?php endif; ?>
    <h1 class="success-message"><?php echo $successMessage; ?></h1>
    <h1 class="title">Torneos Registrados</h1>
    <?php foreach ($tournaments as $tournament): ?>
        <div class="tournament-details">
            <h3><?php echo $tournament['name']; ?> (<?php echo $tournament['tournament_date']; ?>)</h3>

            <?php
            // Mostrar podios por categoría
            $tournament_id = $tournament['id'];
            $sql_results = "SELECT r.position, r.category, p.name, p.last_name
                            FROM results r
                            JOIN players p ON r.player_id = p.id
                            WHERE r.tournament_id = ?
                            ORDER BY r.category, r.position ASC";
            $stmt_results = $conn->prepare($sql_results);
            $stmt_results->bind_param("i", $tournament_id);
            $stmt_results->execute();
            $results = $stmt_results->get_result()->fetch_all(MYSQLI_ASSOC);

            $current_category = "";
            echo "<div class='podio-grid-container'>"; // Contenedor para la grilla de todas las categorías
            foreach ($results as $result) {
                // Si cambia la categoría, imprimimos un título y reiniciamos la grilla para esa categoría
                if ($result['category'] !== $current_category) {
                    if ($current_category !== "") {
                        echo "</div>"; // Cerramos la grilla anterior
                    }
                    $current_category = $result['category'];
                    echo "<div class='podio-title'><h4>Podio - " . ucfirst($current_category) . "</h4></div>";
                    echo "<div class='podio-grid'>"; // Comienza una nueva grilla para los resultados           
                }

                // Determinamos la clase según la posición
                $position_class = '';
                $trophy_icon = ''; // Variable para el ícono del trofeo

                if ($result['position'] == 1) {
                    $position_class = 'position-1'; // Primer lugar
                    $trophy_icon = '<i class="fas fa-trophy"></i>'; // Trofeo dorado
                } elseif ($result['position'] == 2) {
                    $position_class = 'position-2'; // Segundo lugar
                    $trophy_icon = '<i class="fas fa-medal"></i>'; // Medalla
                } elseif ($result['position'] == 3) {
                    $position_class = 'position-3'; // Tercer lugar
                    $trophy_icon = '<i class="fas fa-award"></i>'; // Trofeo
                }

                // Mostrar los jugadores con el ícono del trofeo
                if ($result['position'] <= 3) { // Solo mostramos posiciones 1, 2 y 3
                    echo "<div class='podio-item {$position_class}'>";
                    echo "<p>{$trophy_icon}</p>"; // Mostrar trofeo o medalla
                    echo "<p><strong>Posición:</strong> {$result['position']}</p>";
                    echo "<p><strong>Jugador:</strong> {$result['name']} {$result['last_name']}</p>";
                    echo "</div>"; // Cierra un podio-item
                }
            }
            echo "</div>"; // Cierra la última grilla de resultados
            echo "</div>"; // Cierra el contenedor de la grilla de categorías

            // Mostrar cuarto puesto por separado
            echo "<div class='cuarto-puesto'>";
            foreach ($results as $result) {
                if ($result['position'] == 4) {
                    echo "<div class='cuarto-item'>";
                    echo "<p><strong>Cuarto Puesto:</strong></p>";
                    echo "<p><strong>Jugador:</strong> {$result['name']} {$result['last_name']}</p>";
                    echo "<p><strong>Posición:</strong> {$result['position']}</p>";
                    echo "</div>"; // Cierra cuarto-item
                }
            }
            echo "</div>"; 
            ?>
        </div>

        <?php endforeach; ?>
    </div>
</section>

<?php include 'layout/footer.php'; ?>
