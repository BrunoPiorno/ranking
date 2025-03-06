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
    $reset_sql = "UPDATE players SET points = 0 WHERE id = ?";
    if ($stmt = $conn->prepare($reset_sql)) {
        $stmt->bind_param("i", $player_id);
        if ($stmt->execute()) {
            $message = "El ranking del jugador ha sido reseteado exitosamente.";
        } else {
            $message = "Error al resetear el ranking: " . $stmt->error;
        }
        $stmt->close();
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
    $update_sql = "UPDATE players SET points = points + ?, tournament_position = ? WHERE id = ?";
    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("iii", $bonus_points, $position, $player_id);
        if ($stmt->execute()) {
            $message = "Posición y puntos actualizados correctamente.";
        } else {
            $message = "Error al actualizar la posición del jugador: " . $stmt->error;
        }
        $stmt->close();
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

// Obtener la categoría seleccionada del formulario
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Modificar la consulta SQL dependiendo de la categoría seleccionada
if ($categoria_filtro) {
    // Si hay una categoría seleccionada, filtrar por categoría
    $sql = "SELECT id, name, last_name, points, tournament_position FROM players WHERE (CASE
                WHEN points >= 900 THEN 'Primera'
                WHEN points >= 500 THEN 'Segunda'
                WHEN points >= 300 THEN 'Tercera'
                WHEN points >= 100 THEN 'Cuarta'
                ELSE 'Menores'
            END) = ? ORDER BY points DESC";
} else {
    // Si no hay filtro, mostrar todos los jugadores
    $sql = "SELECT id, name, last_name, points, tournament_position FROM players ORDER BY points DESC";
}

if ($stmt = $conn->prepare($sql)) {
    if ($categoria_filtro) {
        $stmt->bind_param("s", $categoria_filtro);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}
?>

<?php include 'layout/header.php'; ?>
<section class="ranking">
    <div class="container">
        <h1 class="title">Ranking Actual</h1>
        <form method="GET" action="">
            <label for="categoria">Filtrar por categoría:</label>
            <div class="ranking_filter">
                <select name="categoria" id="categoria">
                    <option value="">Todas las Categoría</option>
                    <option value="Primera" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Primera') echo 'selected'; ?>>Primera</option>
                    <option value="Segunda" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Segunda') echo 'selected'; ?>>Segunda</option>
                    <option value="Tercera" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Tercera') echo 'selected'; ?>>Tercera</option>
                    <option value="Cuarta" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Cuarta') echo 'selected'; ?>>Cuarta</option>
                    <option value="Menores" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Menores') echo 'selected'; ?>>Menores</option>
                </select>
                <input type="submit" value="Filtrar">
                <?php if (!empty($_GET['categoria'])): ?>
                    <a href="ranking.php" class="clear-filters-btn">Limpiar Filtro</a>
                <?php endif; ?>
            </div>
        </form>



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
