<?php
// Función de cálculo de puntos
function calculate_points($player1_points, $player2_points, $winner_id, $loser_id, $player1_id, $player2_id) {
    // Obtener los puntos del ganador y perdedor
    $winner_points = ($winner_id == $player1_id) ? $player1_points : $player2_points;
    $loser_points = ($loser_id == $player1_id) ? $player1_points : $player2_points;
    
    // Calcular la diferencia de puntos
    $rating_difference = abs($winner_points - $loser_points);
    
    // Puntos base
    $points_for_winner = 10;
    $points_for_loser = -5;

    // Si el ganador tiene menos puntos que el perdedor, recibe un bonus
    if ($winner_points < $loser_points) {
        // Bonus del 10% de la diferencia para el ganador
        $bonus_points = round($rating_difference * 0.10);
        $points_for_winner += $bonus_points;
    }

    // Si el perdedor tiene MENOS puntos que el ganador, recibe penalización adicional
    if ($loser_points < $winner_points) {
        // Penalización del 5% de la diferencia para el perdedor
        $penalty_points = round($rating_difference * 0.05);
        $points_for_loser -= $penalty_points;
    }

    return [
        'winner_points' => $points_for_winner,
        'loser_points' => $points_for_loser
    ];
}