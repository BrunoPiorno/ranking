<?php
include '../db.php';
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: <?= url('/') ?>login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_id = intval($_POST['player_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $document = $conn->real_escape_string($_POST['document']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $localidad = $conn->real_escape_string($_POST['localidad']);

    // Verificar si el DNI ya existe para otro jugador
    $check_sql = "SELECT id FROM players WHERE document = '$document' AND id != $player_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Ya existe un jugador con ese DNI.";
        header("Location: <?= url('/') ?>players/player_profile.php?id=$player_id");
        exit;
    }

    // Actualizar los datos del jugador
    $sql = "UPDATE players SET 
            name = '$name',
            last_name = '$last_name',
            document = '$document',
            phone = '$phone',
            localidad = '$localidad'
            WHERE id = $player_id";

    if ($conn->query($sql)) {
        $_SESSION['success'] = "Datos del jugador actualizados correctamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar los datos: " . $conn->error;
    }

    header("Location: <?= url('/') ?>players/player_profile.php?id=$player_id");
    exit;
} else {
    header('Location: <?= url('/') ?>ranking.php');
    exit;
}

$conn->close();
?>
