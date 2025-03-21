<?php
include '../db.php';
include '../layout/header.php';

if (isset($_POST['player_id'])) {
    $player_id = intval($_POST['player_id']);

    $sql = "SELECT * FROM players WHERE id = $player_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Eliminar los resultados relacionados con el jugador (si es necesario)
        $delete_results_sql = "DELETE FROM results WHERE player_id = $player_id";
        $conn->query($delete_results_sql);

        $delete_player_sql = "DELETE FROM players WHERE id = $player_id";
        if ($conn->query($delete_player_sql) === TRUE) {
            echo "<script>
                Swal.fire({
                    title: 'Jugador eliminado!',
                    text: 'El jugador ha sido eliminado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#1c4857',
                }).then(function() {
                    window.location.href = '/ranking';
                });
            </script>";
        } else {
            echo "Error al eliminar el jugador: " . $conn->error;
        }
    } else {
        echo "Jugador no encontrado.";
    }

    $conn->close();
} else {
    echo "ID de jugador no especificado.";
}
?>