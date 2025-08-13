<?php
// update_ranking.php

include 'db.php';

// 1. Mueve la posición actual de todos a la columna de posición anterior.
$conn->query("UPDATE players SET previous_position = current_position");

// 2. Obtiene a todos los jugadores ordenados por puntos para establecer el nuevo ranking.
$result = $conn->query("SELECT id FROM players ORDER BY points DESC, last_name ASC, name ASC");

if ($result->num_rows > 0) {
    $position = 1;
    while ($row = $result->fetch_assoc()) {
        $player_id = $row['id'];
        
        // 3. Actualiza la nueva posición actual para cada jugador.
        $update_sql = "UPDATE players SET current_position = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $position, $player_id);
        $stmt->execute();
        
        $position++;
    }
}
?>