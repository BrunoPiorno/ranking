<?php
// Función de cálculo de puntos
function calculate_points($player1_points, $player2_points, $winner_id, $loser_id, $player1_id, $player2_id) {
    $winner_current_points = ($winner_id == $player1_id) ? $player1_points : $player2_points;
    $loser_current_points = ($loser_id == $player1_id) ? $player1_points : $player2_points;

    $points_change = calculate_points_change($winner_current_points, $loser_current_points);

    return [
        'winner_points' => $points_change['winner'],
        'loser_points' => $points_change['loser']
    ];
}

function calculate_points_change($winner_current_points, $loser_current_points) {
    $point_diff = abs($winner_current_points - $loser_current_points);
    $points_for_winner = 0;

    // Lógica para determinar los puntos del ganador según la tabla
    if ($winner_current_points < $loser_current_points) { // Gana el de MENOR puntaje
        if ($point_diff >= 750) $points_for_winner = 28;
        elseif ($point_diff >= 500) $points_for_winner = 26;
        elseif ($point_diff >= 400) $points_for_winner = 24;
        elseif ($point_diff >= 300) $points_for_winner = 22;
        elseif ($point_diff >= 200) $points_for_winner = 20;
        elseif ($point_diff >= 150) $points_for_winner = 18;
        elseif ($point_diff >= 100) $points_for_winner = 16;
        elseif ($point_diff >= 50) $points_for_winner = 14;
        elseif ($point_diff >= 25) $points_for_winner = 12;
        else $points_for_winner = 10; // 0-24
    } else { // Gana el de MAYOR puntaje
        if ($point_diff >= 750) $points_for_winner = 1;
        elseif ($point_diff >= 500) $points_for_winner = 2;
        elseif ($point_diff >= 400) $points_for_winner = 3;
        elseif ($point_diff >= 300) $points_for_winner = 4;
        elseif ($point_diff >= 200) $points_for_winner = 5;
        elseif ($point_diff >= 150) $points_for_winner = 6;
        elseif ($point_diff >= 100) $points_for_winner = 7;
        elseif ($point_diff >= 50) $points_for_winner = 8;
        elseif ($point_diff >= 25) $points_for_winner = 9;
        else $points_for_winner = 10; // 0-24
    }

    // Sistema de suma cero: el perdedor pierde la misma cantidad que el ganador obtiene.
    $points_for_loser = -$points_for_winner;

    return [
        'winner' => $points_for_winner,
        'loser'  => $points_for_loser
    ];
}