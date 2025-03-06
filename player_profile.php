<?php
include 'db.php';
function obtener_categoria($puntos) {
    if ($puntos >= 900) {
        return 'Primera';
    } elseif ($puntos >= 500) {
        return 'Segunda';
    } elseif ($puntos >= 300) {
        return 'Tercera';
    } elseif ($puntos >= 100) {
        return 'Cuarta';
    } else {
        return 'Menores';
    }
}

if (isset($_GET['id'])) {
    $player_id = intval($_GET['id']);

    // Consulta el perfil del jugador
    $sql = "SELECT name, last_name, document, phone, points FROM players WHERE id = $player_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $player = $result->fetch_assoc();
        $points = $player['points'];
        $category = obtener_categoria($points);

        // Posición en el ranking
        $ranking_sql = "SELECT COUNT(*) AS position FROM players WHERE points > (SELECT points FROM players WHERE id = $player_id)";
        $ranking_result = $conn->query($ranking_sql);
        $position_data = $ranking_result->fetch_assoc();
        $position = $position_data['position'] + 1;

        // Ranking dentro de la categoría
        $category_ranking_sql = "SELECT COUNT(*) AS category_position FROM players 
                                 WHERE points > $points 
                                 AND (points >= 900 AND '$category' = 'Primera'
                                     OR points >= 500 AND points < 900 AND '$category' = 'Segunda'
                                     OR points >= 300 AND points < 500 AND '$category' = 'Tercera'
                                     OR points >= 100 AND points < 300 AND '$category' = 'Cuarta'
                                     OR points < 100 AND '$category' = 'Menores')";
        $category_ranking_result = $conn->query($category_ranking_sql);
        $category_position_data = $category_ranking_result->fetch_assoc();
        $category_position = $category_position_data['category_position'] + 1;

        // Historial de partidos
        // $matches_sql = "
        // SELECT m.id, 
        //     p1.name AS player1_name, 
        //     p2.name AS player2_name, 
        //     m.player1_score, 
        //     m.player2_score, 
        //     m.match_date, 
        //     m.results, 
        //     m.set_1_score, 
        //     m.set_2_score, 
        //     m.set_3_score, 
        //     m.winner_id
        // FROM matches m
        // JOIN players p1 ON m.player1_id = p1.id
        // JOIN players p2 ON m.player2_id = p2.id
        // WHERE m.player1_id = $player_id OR m.player2_id = $player_id
        // ORDER BY m.match_date DESC";
        // $matches_result = $conn->query($matches_sql);

    } else {
        echo "Jugador no encontrado.";
        exit;
    }
} else {
    echo "ID de jugador no especificado.";
    exit;
}

$conn->close();
?>

<?php include 'layout/header.php'; ?>
<section class="player-profile">
    <div class="container">
        <!-- Card principal del perfil del jugador -->
        <div class="player-card">
            <div class="player-card__header">
                <div class="player-card__details">
                    <h2><?php echo htmlspecialchars($player['name'] . ' ' . $player['last_name']); ?></h2>
                    <p>Posición en el Ranking General: #<?php echo htmlspecialchars($position); ?></p>
                    <p>Posición en el Ranking de <?php echo htmlspecialchars($category); ?>: #<?php echo htmlspecialchars($category_position); ?></p>
                    <p>Puntos: <?php echo htmlspecialchars($player['points']); ?></p>
                </div>
            </div>
            <div class="player-card__info">
                <p><strong>Documento (DNI):</strong> <?php echo htmlspecialchars($player['document']); ?></p>                
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($player['phone']); ?></p>
            </div>
        </div>

        <!-- Historial de partidos -->
        <!-- <div class="player-history">
            <h3 class="player-history__title">Historial de Partidos</h3>
            <?php if ($matches_result->num_rows > 0) : ?>
                <ul class="match-list">
                    <?php while ($match = $matches_result->fetch_assoc()) : ?>
                        <li class="match-list__item">
                            <div class="match-list__header">
                                <p><strong>Partido:</strong> <?php echo $match['player1_name']; ?> (<?php echo $match['player1_score']; ?>) vs. <?php echo $match['player2_name']; ?> (<?php echo $match['player2_score']; ?>)</p>
                                <p><strong>Fecha:</strong> <?php echo $match['match_date']; ?></p>
                            </div>
                            <div class="match-list__details">
                            <p><strong>Set 1:</strong> <?php echo isset($match['set_1_score']) ? $match['set_1_score'] : 'No disponible'; ?></p>
                            <p><strong>Set 2:</strong> <?php echo isset($match['set_2_score']) ? $match['set_2_score'] : 'No disponible'; ?></p>
                            <p><strong>Set 3:</strong> <?php echo isset($match['set_3_score']) ? $match['set_3_score'] : 'No disponible'; ?></p>

                            <p><strong>Resultado:</strong> <?php
                            if (isset($match['winner_id'])) {
                                echo $match['winner_id'] == $player_id ? 'Ganaste' : 'Perdiste';
                                echo " y obtuviste " . ($match['winner_id'] == $player_id ? '5' : '0') . " puntos";
                            } else {
                                echo 'Resultado no disponible';
                            }
                            ?></p>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else : ?>
                <p class="player-history__no-matches">No hay historial de partidos para este jugador.</p>
            <?php endif; ?>
        </div> -->
    </div>
</section>
<?php include 'layout/footer.php'; ?>
