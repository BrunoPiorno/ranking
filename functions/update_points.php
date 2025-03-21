<?php
include 'db.php';
include 'calculate_points.php';

function update_player_points($player1_id, $player2_id, $winner) {
    global $conn;

    // Obtener los puntos actuales de cada jugador
    $stmt = $conn->prepare("SELECT id, init_point FROM players WHERE id = ? OR id = ?");
    $stmt->bind_param("ii", $player1_id, $player2_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $players = [];
    while ($row = $result->fetch_assoc()) {
        $players[$row['id']] = $row['init_point'];
    }

    // Calcular los puntos de acuerdo al resultado
    $player1_points = $players[$player1_id];
    $player2_points = $players[$player2_id];
    
    $points = calculate_points($player1_points, $player2_points, $winner);

    // Actualizar los puntos en la base de datos
    $stmt = $conn->prepare("UPDATE players SET init_point = init_point + ? WHERE id = ?");
    $stmt->bind_param("ii", $points['winner_points'], $winner);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE players SET init_point = init_point + ? WHERE id = ?");
    $stmt->bind_param("ii", $points['loser_points'], $winner === $player1_id ? $player2_id : $player1_id);
    $stmt->execute();
}
