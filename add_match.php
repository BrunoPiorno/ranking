<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';
include 'functions/calculate_points.php';

// Funciones para obtener y actualizar puntos
function get_player_points($conn, $player_id) {
    $sql = "SELECT points FROM players WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $player_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['points'] : 0;
}

function update_player_points($conn, $player_id, $points) {
    $sql = "UPDATE players SET points = points + ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $points, $player_id);
    $stmt->execute();
}

// Función para obtener el nombre del jugador
function get_player_name($conn, $player_id) {
    $sql = "SELECT name, last_name FROM players WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $player_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['name'] . ' ' . $row['last_name'] : 'Jugador no encontrado';
}

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player1_id = isset($_POST['player1_id']) ? $_POST['player1_id'] : null;
    $player2_id = isset($_POST['player2_id']) ? $_POST['player2_id'] : null;

    if ($player1_id === null || $player2_id === null) {
        echo "<p>Por favor, seleccione ambos jugadores antes de registrar el partido.</p>";
        return;
    }

    // Inicializamos el arreglo para los sets
    $sets = [];
    for ($i = 1; $i <= 5; $i++) {
        $set_player1 = isset($_POST["set$i"][0]) ? (int)$_POST["set$i"][0] : 0;
        $set_player2 = isset($_POST["set$i"][1]) ? (int)$_POST["set$i"][1] : 0;
        
        $sets[] = [
            'player1' => $set_player1,
            'player2' => $set_player2
        ];
    }

    // Contadores para las victorias de cada jugador
    $player1_wins = 0;
    $player2_wins = 0;

    foreach ($sets as $set) {
        if ($set['player1'] > $set['player2']) {
            $player1_wins++;
        } elseif ($set['player2'] > $set['player1']) {
            $player2_wins++;
        }
    }

    $winner_id = $player1_wins > $player2_wins ? $player1_id : $player2_id;
    $loser_id = $winner_id == $player1_id ? $player2_id : $player1_id;
    $final_score = "{$player1_wins}-{$player2_wins}";

    $player1_current_points = get_player_points($conn, $player1_id);
    $player2_current_points = get_player_points($conn, $player2_id);

    // Llamamos a la función de cálculo de puntos
    $points = calculate_points($player1_current_points, $player2_current_points, $winner_id, $loser_id, $player1_id, $player2_id);
    
    if ($points['loser_points'] < 0) {
        // Evitar que los puntos del perdedor bajen de 100
        $loser_current_points = get_player_points($conn, $loser_id);
        if ($loser_current_points + $points['loser_points'] < 100) {
            $points['loser_points'] = 0;  // Establecer 0 puntos adicionales si intentan bajar de 100
        }
    }
    
    update_player_points($conn, $winner_id, $points['winner_points']);
    update_player_points($conn, $loser_id, $points['loser_points']);

    $winner_name = get_player_name($conn, $winner_id);
    $loser_name = get_player_name($conn, $loser_id);

    $sql = "INSERT INTO matches (player1_id, player2_id, player1_score, player2_score, match_date, results) 
            VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $player1_score = $player1_wins;
    $player2_score = $player2_wins;
    $results = "Victoria de " . ($winner_id == $player1_id ? $winner_name : $loser_name);
    $stmt->bind_param("iiiis", $player1_id, $player2_id, $player1_score, $player2_score, $results);
    $stmt->execute();
}

include('layout/header.php');?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<section class="add_match">
    <div class="container">
        <h1 class="title">Registrar Partido</h1>
        <form class="form" action="add_match.php" method="post">
            <div class="player_cont">
                <div class="player_item">
                    <label for="player1_id">Jugador 1:</label>
                    <select name="player1_id" class="select2-player" required>
                        <option value="">Buscar jugador...</option>
                        <?php
                        $result = $conn->query("SELECT id, name, last_name FROM players ORDER BY last_name ASC");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['last_name']}, {$row['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            
                <div class="player_item">
                    <label for="player2_id">Jugador 2:</label>
                    <select name="player2_id" class="select2-player" required>
                        <option value="">Buscar jugador...</option>
                        <?php
                        $result = $conn->query("SELECT id, name, last_name FROM players ORDER BY last_name ASC");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['last_name']}, {$row['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>


            <div id="sets">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="set">
                    <label for="set<?= $i ?>">Set <?= $i ?>:</label>
                    <input type="number" name="set<?= $i ?>[0]" min="0" placeholder="Jugador 1" >
                    <input type="number" name="set<?= $i ?>[1]" min="0" placeholder="Jugador 2" >
                </div>
                <?php endfor; ?>
            </div>

            <input type="submit" value="Registrar Partido">
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="message">
                <p>Partido registrado. Jugador <strong><?php echo $winner_name; ?></strong> ganó, con un resultado de <strong><?php echo $final_score; ?></strong>. Se le otorgaron <strong><?php echo $points['winner_points']; ?></strong> puntos. Jugador <strong><?php echo $loser_name; ?></strong> recibió <strong><?php echo $points['loser_points']; ?></strong> puntos.</p>
                <a href="ranking.php" class="btn">Ver Tabla de Ranking</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="/ranking/js/select2-init.js"></script>
<?php include('layout/footer.php'); ?>