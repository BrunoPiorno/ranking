<?php
include 'db.php';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['player_id'])) {
    $player_id = intval($_POST['player_id']); // Asegúrate de que sea un entero

    // Consulta para resetear los puntos del jugador a 0
    $sql = "UPDATE players SET points = 0 WHERE id = $player_id";

    // Intentar ejecutar la consulta
    if ($conn->query($sql) === TRUE) {
        echo "<p>El ranking del jugador ha sido reseteado exitosamente.</p>";
    } else {
        echo "<p>Error al resetear el ranking: " . $conn->error . "</p>";
    }
} else {
    echo "<p>No se ha recibido ningún ID de jugador.</p>";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
