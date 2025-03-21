<?php
// Función de cálculo de puntos
function calculate_points($player1_points, $player2_points, $winner_id, $loser_id, $player1_id, $player2_id) {
    // Tabla de puntos según la diferencia de puntos
    $rating_difference = abs($player1_points - $player2_points);
    
    // Variables para almacenar los puntos finales del ganador y perdedor
    $winner_points = 0;
    $loser_points = 0;

    // Verificar si el ganador tiene menos puntos que el perdedor
    if (($winner_id == $player1_id && $player1_points < $player2_points) || 
        ($winner_id == $player2_id && $player2_points < $player1_points)) {
        
        // Calcular la bonificación del ganador (10% de la diferencia de puntos)
        $bonus_points = $rating_difference * 0.10; // Bonificación del 10% de la diferencia
        $winner_points = 10 + $bonus_points; // El ganador obtiene 10 puntos + la bonificación
        $loser_points = -5 - ($rating_difference * 0.05); // El perdedor pierde 5 puntos + penalización (5% de la diferencia)
    } else {
        // Si el ganador tiene más puntos que el perdedor
        $winner_points = 10; // El ganador recibe 10 puntos
        $loser_points = -5; // El perdedor pierde 5 puntos
    }

    // Retornar los puntos calculados para el ganador y el perdedor
    return ['winner_points' => $winner_points, 'loser_points' => $loser_points];
}