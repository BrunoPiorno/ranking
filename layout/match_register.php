<?php
include 'db.php';?>

<form method="POST" action="match_register.php">
    <label for="player1_id">Jugador 1</label>
    <input type="text" id="player1_id" name="player1_id">
    
    <label for="player2_id">Jugador 2</label>
    <input type="text" id="player2_id" name="player2_id">
    
    <label for="player1_wins">Puntuación Jugador 1</label>
    <input type="text" id="player1_wins" name="player1_wins">
    
    <label for="player2_wins">Puntuación Jugador 2</label>
    <input type="text" id="player2_wins" name="player2_wins">
    
    <input type="submit" value="Registrar Partido">
</form>
