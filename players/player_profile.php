<?php
include '../db.php';
include '../functions/obtener_categoria.php';  

if (isset($_GET['id'])) {
    $player_id = intval($_GET['id']);

    // Consulta el perfil del jugador
    $sql = "SELECT id, name, last_name, document, phone, points, localidad FROM players WHERE id = $player_id";
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
                                 AND (points >= 800 AND '$category' = 'Primera'
                                     OR points >= 500 AND points < 800 AND '$category' = 'Segunda'
                                     OR points >= 300 AND points < 500 AND '$category' = 'Tercera'
                                     OR points >= 100 AND points < 300 AND '$category' = 'Cuarta'
                                     OR points < 100 AND '$category' = 'Menores')";
        $category_ranking_result = $conn->query($category_ranking_sql);
        $category_position_data = $category_ranking_result->fetch_assoc();
        $category_position = $category_position_data['category_position'] + 1;

        // Obtener victorias del jugador
        $victories_sql = "SELECT COUNT(*) AS victories 
                         FROM matches 
                         WHERE ((player1_id = $player_id AND results = 'Victoria de Jugador 1') 
                         OR (player2_id = $player_id AND results = 'Victoria de Jugador 2'))";
        $victories_result = $conn->query($victories_sql);
        $victories_data = $victories_result->fetch_assoc();
        $victories = $victories_data['victories'];

        // Obtener derrotas del jugador
        $defeats_sql = "SELECT COUNT(*) AS defeats 
                       FROM matches 
                       WHERE ((player1_id = $player_id AND results = 'Victoria de Jugador 2') 
                       OR (player2_id = $player_id AND results = 'Victoria de Jugador 1'))";
        $defeats_result = $conn->query($defeats_sql);
        $defeats_data = $defeats_result->fetch_assoc();
        $defeats = $defeats_data['defeats'];

        $results_sql = "SELECT r.tournament_id, r.position, r.category, t.name AS tournament_name
                FROM results r
                JOIN tournaments t ON r.tournament_id = t.id
                WHERE r.player_id = $player_id AND r.position <= 3";  // Posiciones 1, 2, 3 son el podio
        $results_result = $conn->query($results_sql);


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


include '../layout/header.php'; 

// Mostrar mensajes de éxito o error con SweetAlert
if (isset($_SESSION['success'])) {
    echo "
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '" . htmlspecialchars($_SESSION['success']) . "',
                confirmButtonColor: '#1c4857'
            });
        });
    </script>
    ";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '" . htmlspecialchars($_SESSION['error']) . "',
                confirmButtonColor: '#f44336'
            });
        });
    </script>
    ";
    unset($_SESSION['error']);
}
?>

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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="edit-btn" onclick="toggleEditForm()">Editar Jugador</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="player-card__info">
                <p><strong>Documento (DNI):</strong> <?php echo htmlspecialchars($player['document']); ?></p>                
                <!-- <p><strong>Teléfono:</strong> <?php //echo htmlspecialchars($player['phone']); ?></p> -->
                <p><strong>Localidad:</strong> <?php echo htmlspecialchars($player['localidad']); ?></p>
            </div>

            <!-- Formulario de edición -->
            <div id="editForm" style="display: none;" class="edit-form">
                <h3>Editar Información del Jugador</h3>
                <form id="updatePlayerForm" action="/players/update_player.php" method="POST">
                    <input type="hidden" name="player_id" value="<?php echo htmlspecialchars($player['id']); ?>">
                    
                    <div class="form_edit">
                        <div class="form-group">
                            <label for="name">Nombre:</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($player['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Apellido:</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($player['last_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="document">DNI:</label>
                            <input type="text" id="document" name="document" value="<?php echo htmlspecialchars($player['document']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Teléfono:</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($player['phone']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="localidad">Localidad:</label>
                            <input type="text" id="localidad" name="localidad" value="<?php echo htmlspecialchars($player['localidad']); ?>" required>
                        </div>
                    </div>


                    <div class="form-actions">
                        <button type="submit" class="save-btn">Guardar Cambios</button>
                        <button type="button" class="cancel-btn" onclick="toggleEditForm()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="player-card">
            <div class="player-card__stats">
                <p>Victorias: <?php echo htmlspecialchars($victories); ?></p>
                <p>Derrotas: <?php echo htmlspecialchars($defeats); ?></p>
            </div>
        </div>

        <?php if ($results_result->num_rows > 0): ?>
            <div class="player-podium">
                <h3 class="player-podium__title">Torneos en el Podio</h3>
            
                <ul class="podium-list">
                    <?php while ($result = $results_result->fetch_assoc()): ?>
                        <li class="podium-list__item">
                            <p><strong>Torneo:</strong> <?php echo htmlspecialchars($result['tournament_name']); ?></p>
                            <p><strong>Posición:</strong> <?php echo htmlspecialchars($result['position']); ?></p>
                            <p><strong>Categoría:</strong> <?php echo ucfirst(htmlspecialchars($result['category'])); ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Botón para eliminar al jugador -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="delete_player.php" method="POST" id="delete-form">
                <input type="hidden" name="player_id" value="<?php echo $player_id; ?>">
                <button class="button" type="submit">Eliminar Jugador</button>
            </form>
        <?php endif; ?>
        <script>
            document.getElementById('delete-form').addEventListener('submit', function(e) {
                e.preventDefault(); 

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¡No podrás revertir esta acción!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#1c4857',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        </script>
    </div>
</section>
<script>
function toggleEditForm() {
    const editForm = document.getElementById('editForm');
    editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
}

document.getElementById('updatePlayerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Estás seguro que deseas realizar la modificación?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4CAF50',
        cancelButtonColor: '#f44336',
        confirmButtonText: 'Sí, modificar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});
</script>

<?php include '../layout/footer.php'; ?>
