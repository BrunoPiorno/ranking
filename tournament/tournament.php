<?php
session_start();
include '../layout/header.php';
include '../db.php';

$successMessage = "";
$errorMessage = "";
$tournament_details = null;
$tournament_results = [];

// Obtener años únicos de los torneos
$years_query = "SELECT DISTINCT YEAR(tournament_date) as year FROM tournaments ORDER BY year DESC";
$years_result = $conn->query($years_query);
$years = $years_result->fetch_all(MYSQLI_ASSOC);

// Obtener el año seleccionado o usar el actual
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

$sql_tournaments = "SELECT t.*, COUNT(r.id) as total_players 
                   FROM tournaments t 
                   LEFT JOIN results r ON t.id = r.tournament_id";

if ($selectedYear != 'all') {
    $sql_tournaments .= " WHERE YEAR(t.tournament_date) = ?";
}

$sql_tournaments .= " GROUP BY t.id ORDER BY t.tournament_date DESC";

$stmt = $conn->prepare($sql_tournaments);
if ($selectedYear != 'all') {
    $stmt->bind_param("i", $selectedYear);
}
$stmt->execute();
$tournaments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


// Obtener la lista de jugadores
$sql_players = "SELECT id, name, last_name FROM players";
$result_players = $conn->query($sql_players);
$players = [];
while ($row = $result_players->fetch_assoc()) {
    $players[] = $row;
}
$category_order = ['menores', 'cuarta', 'tercera', 'segunda', 'Primera'];

// Agregar torneo
if (isset($_POST['add_tournament'])) {
    $tournament_name = $_POST['tournament_name'];
    $tournament_date = $_POST['tournament_date'];

    $sql_insert_tournament = "INSERT INTO tournaments (name, tournament_date) VALUES (?, ?)";
    $stmt_insert_tournament = $conn->prepare($sql_insert_tournament);
    $stmt_insert_tournament->bind_param("ss", $tournament_name, $tournament_date);
    
    if ($stmt_insert_tournament->execute()) {
        $_SESSION['success_message'] = "Torneo agregado con éxito";
    } else {
        $_SESSION['error_message'] = "Error al agregar el torneo";
    }
    header("Location: tournament.php");
    exit;
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

    if ($stmt_insert->execute()) {
        // Actualizar los puntos de los jugadores según su posición
        foreach ($players as $position => $player) {
            $points = calculate_points($position + 1); // Obtener puntos según la posición
            
            // Actualizar puntos del jugador
            $sql_update_points = "UPDATE players SET points = IFNULL(points, 0) + ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update_points);
            $stmt_update->bind_param("ii", $points, $player);
            $stmt_update->execute();
        }
        
        $_SESSION['success_message'] = "Resultados agregados con éxito";
    } else {
        $_SESSION['error_message'] = "Error al agregar los resultados";
    }
    header("Location: tournament.php");
    exit;
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="..//js/select2-init.js"></script>
<section class="torneos">
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="tournament-cont">
            <div class="tournament-section">
                <h1><i class="fas fa-trophy"></i> Registrar Torneo</h1>
                
                <?php if ($successMessage): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="tournament-form">
                    <div class="form-group">
                        <label for="tournament_name">
                            <i class="fas fa-pencil-alt"></i> Nombre del Torneo:
                        </label>
                        <input type="text" id="tournament_name" name="tournament_name" required 
                            placeholder="Ingrese el nombre del torneo">
                    </div>

                    <div class="form-group">
                        <label for="tournament_date">
                            <i class="fas fa-calendar"></i> Fecha del Torneo:
                        </label>
                        <input type="date" id="tournament_date" name="tournament_date" required>
                    </div>

                    <button type="submit" name="add_tournament" class="submit-btn">
                        <i class="fas fa-plus-circle"></i> Registrar Torneo
                    </button>
                </form>
            </div>

            <div class="tournament-section">
                <h1><i class="fas fa-medal"></i> Cargar Resultados del Torneo</h1>
                <form method="POST" class="tournament-form">
                    <div class="form-group">
                        <label for="tournament_id">
                            <i class="fas fa-list"></i> Seleccionar Torneo:
                        </label>
                        <select id="tournament_id" name="tournament_id" required>
                            <option value="">Seleccione un torneo</option>
                            <?php foreach ($tournaments as $tournament): ?>
                                <option value="<?php echo $tournament['id']; ?>">
                                    <?php echo htmlspecialchars($tournament['name']) . ' (' . $tournament['tournament_date'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="result_category">
                            <i class="fas fa-layer-group"></i> Categoría:
                        </label>
                        <select id="result_category" name="category" required>
                            <?php foreach ($category_order as $cat): ?>
                                <option value="<?php echo $cat; ?>">
                                    <?php echo ucfirst($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="results-grid">
                        <?php 
                        $positions = [
                            1 => ['text' => '1er Puesto', 'icon' => 'trophy'],
                            2 => ['text' => '2do Puesto', 'icon' => 'medal'],
                            3 => ['text' => '3er Puesto', 'icon' => 'award'],
                            4 => ['text' => '4to Puesto', 'icon' => 'star']
                        ];
                        
                        foreach ($positions as $pos => $details): ?>
                            <div class="form-group">
                                <label for="player<?php echo $pos; ?>">
                                    <i class="fas fa-<?php echo $details['icon']; ?>"></i> 
                                    <?php echo $details['text']; ?>:
                                </label>
                                <select class="select2-player" id="player<?php echo $pos; ?>" name="player<?php echo $pos; ?>" 
                                        required>
                                    <option value="">Buscar jugador...</option>
                                    <?php foreach ($players as $player): ?>
                                        <option value="<?php echo $player['id']; ?>">
                                            <?php echo htmlspecialchars($player['name'] . ' ' . $player['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" name="add_result" class="submit-btn">
                        <i class="fas fa-save"></i> Guardar Resultados
                    </button>
                </form>
            </div>
        </div>

        <?php endif; ?>
        <h1 class="title">Torneos Registrados</h1>
        <label class="title">Filtrar por año</label>
        <div class="ranking_filter-tournament">
            <select id="yearSelect" name="year" class="select2">
                <option value="all" <?php echo $selectedYear == 'all' ? 'selected' : ''; ?>>Ver todos</option>
                <?php foreach ($years as $year): ?>
                    <option value="<?php echo $year['year']; ?>" 
                            <?php echo $selectedYear == $year['year'] ? 'selected' : ''; ?>>
                        <?php echo $year['year']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php foreach ($tournaments as $tournament): ?>
            <div class="tournament-details">
                <h3 class="tournament_title"><?php echo $tournament['name']; ?> (<?php echo $tournament['tournament_date']; ?>)</h3>

                <?php
                // Obtener los resultados de este torneo
                $tournament_id = $tournament['id'];
                $sql_results = "SELECT r.position, r.category, p.name, p.last_name
                                FROM results r
                                JOIN players p ON r.player_id = p.id
                                WHERE r.tournament_id = ?
                                ORDER BY r.position ASC";
                $stmt_results = $conn->prepare($sql_results);
                $stmt_results->bind_param("i", $tournament_id);
                $stmt_results->execute();
                $results = $stmt_results->get_result()->fetch_all(MYSQLI_ASSOC);

                // Agrupar los resultados por categoría
                $grouped_results = [];
                foreach ($results as $result) {
                    $grouped_results[$result['category']][] = $result;
                }

                echo "<div class='podio-grid-container'>"; // Contenedor general

                // Mostrar los podios en el orden deseado
                foreach ($category_order as $category) {
                    if (isset($grouped_results[$category])) {
                        echo "<div class='podio-container'>";
                        echo "<div class='podio-title'><h4>Podio - " . ucfirst($category) . "</h4></div>";
                        echo "<div class='podio-grid'>"; // Inicia la grilla de podio

                        foreach ($grouped_results[$category] as $result) {
                            // Mostrar solo posiciones 1, 2 y 3 en el podio
                            if ($result['position'] <= 3) {
                                $position_class = '';
                                $trophy_icon = '';

                                if ($result['position'] == 1) {
                                    $position_class = 'position-1';
                                    $trophy_icon = '<i class="fas fa-trophy"></i>';
                                } elseif ($result['position'] == 2) {
                                    $position_class = 'position-2';
                                    $trophy_icon = '<i class="fas fa-medal"></i>';
                                } elseif ($result['position'] == 3) {
                                    $position_class = 'position-3';
                                    $trophy_icon = '<i class="fas fa-award"></i>';
                                }

                                echo "<div class='podio-item {$position_class}'>";
                                echo "<p>{$trophy_icon}</p>";
                                echo "<p><strong>Posición:</strong> {$result['position']}</p>";
                                echo "<p><strong>Jugador:</strong> {$result['name']} {$result['last_name']}</p>";
                                echo "</div>"; // Cierra podio-item
                            }
                        }

                        echo "</div>"; // Cierra podio-grid

                        // Mostrar el cuarto puesto justo debajo del podio
                        echo "<div class='cuarto-puesto'>";
                        foreach ($grouped_results[$category] as $result) {
                            if ($result['position'] == 4) {
                                echo "<div class='cuarto-item'>";
                                echo "<p><strong>Cuarto Puesto:</strong></p>";
                                echo "<p><strong>Jugador:</strong> {$result['name']} {$result['last_name']}</p>";
                                echo "</div>"; 
                            }
                        }
                        echo "</div>"; // Cierra cuarto-puesto
                        echo "</div>"; // Cierra podio-container
                    }
                }

                echo "</div>"; // Cierra podio-grid-container
                ?>
            </div>
        <?php endforeach; ?>
        </div>
    </section>
<?php include '../layout/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: '<?php echo $_SESSION['success_message']; ?>',
            showConfirmButton: true
        }).then(() => {
            <?php unset($_SESSION['success_message']); ?>
        });
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        Swal.fire({
            icon: 'error',
            title: '<?php echo $_SESSION['error_message']; ?>',
            showConfirmButton: true
        }).then(() => {
            <?php unset($_SESSION['error_message']); ?>
        });
    <?php endif; ?>
});
</script>
<script src="<?= url('/') ?>js/select2-init.js"></script>