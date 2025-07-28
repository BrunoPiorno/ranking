<?php
// Función de cálculo de puntos
function calculate_points($player1_points, $player2_points, $winner_id, $loser_id, $player1_id, $player2_id) {
    $winner_current_points = ($winner_id == $player1_id) ? $player1_points : $player2_points;
    $loser_current_points = ($loser_id == $player1_id) ? $player1_points : $player2_points;
    $rating_difference = abs($winner_current_points - $loser_current_points);

    $points_for_winner = 0;
    $points_for_loser = 0;

    // CASO 1: El ganador tiene MENOS puntos que el perdedor (victoria meritoria)
    if ($winner_current_points < $loser_current_points) {
        // Regla #1: Ganas contra alguien con mayor puntaje
        $bonus = round($rating_difference * 0.10);
        $points_for_winner = 10 + $bonus;
        
        // Regla #3: Pierdes contra alguien con menor puntaje (el perdedor es el de más puntos)
        $penalty = round($rating_difference * 0.05);
        $points_for_loser = -5 - $penalty;

    // CASO 2: El ganador tiene MÁS puntos que el perdedor (victoria esperable)
    } else {
        // Regla #2: Ganas contra alguien con menor puntaje
        $points_for_winner = 10;

        // Regla #4: Pierdes contra alguien con mayor puntaje
        $points_for_loser = -5;
    }

    // Límite de pérdida: un jugador nunca puede perder más de 30 puntos
    if ($points_for_loser < -30) {
        $points_for_loser = -30;
    }

    return [
        'winner_points' => $points_for_winner,
        'loser_points' => $points_for_loser
    ];
}