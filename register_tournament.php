<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Acceso restringido.";
    exit;
}

// Verificar si se envió el formulario
if (isset($_POST['add_tournament'])) {
    $tournament_name = $_POST['tournament_name'];
    $tournament_date = $_POST['tournament_date'];

    // Insertar el torneo en la base de datos
    $sql_insert_tournament = "INSERT INTO tournaments (name, tournament_date) VALUES (?, ?)";
    $stmt_insert_tournament = $conn->prepare($sql_insert_tournament);
    $stmt_insert_tournament->bind_param("ss", $tournament_name, $tournament_date);
    
    if ($stmt_insert_tournament->execute()) {
        // Redirigir de nuevo a tournament.php con un mensaje de éxito
        header("Location: tournament.php?success=1");
        exit;
    } else {
        // Redirigir con un mensaje de error
        header("Location: tournament.php?error=1");
        exit;
    }
}

header("Location: tournament.php");
exit;
?>
