<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

include 'db.php';

// Consultar todos los jugadores ordenados por puntos de mayor a menor
$sql = "SELECT id, name, last_name, points, tournament_position FROM players ORDER BY points DESC";
$result = $conn->query($sql);

// Comprobar si se ha enviado la solicitud para resetear puntos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_player_id'])) {
    $player_id = intval($_POST['reset_player_id']); // Asegúrate de que sea un entero

    // Consulta para resetear los puntos del jugador a 0
    $reset_sql = "UPDATE players SET points = 0 WHERE id = $player_id";

    // Intentar ejecutar la consulta
    if ($conn->query($reset_sql) === TRUE) {
        $message = "El ranking del jugador ha sido reseteado exitosamente.";
    } else {
        $message = "Error al resetear el ranking: " . $conn->error;
    }
}

// Actualizar los puntos de acuerdo a la posición en el torneo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_position'])) {
    $player_id = $_POST['player_id'];
    $position = $_POST['tournament_position'];

    // Definir los puntos según la posición en el torneo
    switch ($position) {
        case 1:
            $bonus_points = 30;  // Campeón
            break;
        case 2:
            $bonus_points = 25;  // Sub-Campeón
            break;
        case 3:
            $bonus_points = 21;  // Tercero
            break;
        case 4:
            $bonus_points = 17;  // 4° de final
            break;
        default:
            $bonus_points = 0;   // Sin puntos adicionales
            break;
    }

    // Actualizar puntos del jugador
    $update_sql = "UPDATE players SET points = points + $bonus_points, tournament_position = $position WHERE id = $player_id";
    if ($conn->query($update_sql) === TRUE) {
        $message = "Posición y puntos actualizados correctamente.";
    } else {
        $message = "Error al actualizar la posición del jugador: " . $conn->error;
    }
}

function obtener_categoria($puntos) {
    if ($puntos >= 900) {
        return 'Primera';
    } elseif ($puntos >= 500) {
        return 'Segunda';
    } elseif ($puntos >= 300) {
        return 'Tercera';
    } elseif ($puntos >= 100) {
        return 'Cuarta';
    } else {
        return 'Menores';
    }
}
?>

<?php include 'layout/header.php'; ?>
<section class="ranking">
    <div class="container">
        <h1 class="title">Ranking Actual</h1>
        <table>
            <tr>
                <th>Posición</th>
                <th>Nombre</th>
                <th>Puntos</th>
                <th>Categoría</th>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <th>Posición en Torneo</th>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                $position = 1;
                while ($row = $result->fetch_assoc()) {
                    // Verifica si existe el apellido y muestra el nombre completo
                    $full_name = htmlspecialchars($row['last_name']);
                    if (isset($row['last_name'])) {
                        $full_name .= ' ' . htmlspecialchars($row['name']);
                    }

                    $categoria = obtener_categoria($row['points']);

                    echo "<tr>
                            <td>{$position}</td>
                            <td><a class='link_profile' href='player_profile.php?id={$row['id']}'>$full_name</a></td>
                            <td>{$row['points']}</td>
                            <td>{$categoria}</td>";

                    if (isset($_SESSION['user_id'])) {
                        echo "<td>
                                <form class='tournament_position' action='' method='post'>
                                    <input type='hidden' name='player_id' value='{$row['id']}'>
                                    <select name='tournament_position'>
                                        <option value='1' ".($row['tournament_position'] == 1 ? 'selected' : '').">1</option>
                                        <option value='2' ".($row['tournament_position'] == 2 ? 'selected' : '').">2</option>
                                        <option value='3' ".($row['tournament_position'] == 3 ? 'selected' : '').">3</option>
                                        <option value='4' ".($row['tournament_position'] == 4 ? 'selected' : '').">4</option>
                                        <option value='0' ".($row['tournament_position'] == 0 ? 'selected' : '').">Sin Puntos</option>
                                    </select>
                                    <input type='submit' name='update_position' value='Actualizar'>
                                </form>
                              </td>
                              <td>
                                <form action='' method='post' onsubmit='return confirm(\"¿Estás seguro de que deseas resetear el ranking de este jugador?\");'>
                                    <input type='hidden' name='reset_player_id' value='{$row['id']}'>
                                    <input type='submit' value='Resetear Ranking'>
                                </form>
                              </td>";
                    }

                    echo "</tr>";
                    $position++;
                }
            } else {
                echo "<tr><td colspan='5'>No hay jugadores registrados.</td></tr>";
            }
            ?>
        </table>
    </div>
</section>
<?php $conn->close(); ?>
<?php include 'layout/footer.php'; ?>
