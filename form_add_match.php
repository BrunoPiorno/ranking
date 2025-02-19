<?php include 'layout/header.php'; ?>

<section class="add_match">
    <div class="container">
    <h1>Registrar Jugador</h1>
    <form class="form" action="add_match.php" method="post">
        <label for="player1_id">Jugador 1:</label>
        <select name="player1_id" required>
            <option value="">Seleccione un jugador</option>
            <?php
            include 'db.php';
            // Obtener todos los jugadores de la base de datos
            $result = $conn->query("SELECT id, name FROM players");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select>

        <label for="player2_id">Jugador 2:</label>
        <select name="player2_id" required>
            <option value="">Seleccione un jugador</option>
            <?php
            // Obtener todos los jugadores de la base de datos
            $result = $conn->query("SELECT id, name FROM players");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select>

        <h3>Resultados por Set (Máximo 5 Sets)</h3>
        <div id="sets">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="set">
                <label for="set<?= $i ?>">Set <?= $i ?>:</label>
                <input type="number" name="set<?= $i ?>[0]" placeholder="Puntos Jugador 1" required>
                <input type="number" name="set<?= $i ?>[1]" placeholder="Puntos Jugador 2" required>
            </div>
            <?php endfor; ?>
        </div>

        <input type="submit" value="Registrar Partido">
    </form>
    </div>

</section>
<?php include 'layout/footer.php'; ?>