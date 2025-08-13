<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

$results_per_page = 30; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $results_per_page; 

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';

$sql = "SELECT id, name, last_name, points, tournament_position, localidad FROM players";
$params = [];
$types = "";
$where_clauses = [];

if (!empty($search)) {
    $where_clauses[] = "(name LIKE ? OR last_name LIKE ? OR localidad LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "sss";    
}

if ($categoria_filtro) {
    $where_clauses[] = "(SELECT c.name 
                         FROM categories c 
                         WHERE players.points >= c.min_points 
                         ORDER BY c.min_points DESC 
                         LIMIT 1) = ?";
    $params[] = $categoria_filtro;
    $types .= "s";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY points DESC, last_name ASC, name ASC LIMIT ? OFFSET ?";
$params[] = $results_per_page; 
$params[] = $offset;  // OFFSET
$types .= "ii";  // Para los dos parámetros de tipo entero (LIMIT y OFFSET);

if ($stmt = $conn->prepare($sql)) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

}
$total_sql = "SELECT COUNT(*) FROM players";
$total_result = $conn->query($total_sql);
$total_players = $total_result->fetch_row()[0]; // Obtén el número total de jugadores
$total_pages = ceil($total_players / $results_per_page); // Calcula el total de páginas


// Comprobar si se ha enviado la solicitud para resetear puntos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_player_id'])) {
    $player_id = intval($_POST['reset_player_id']);

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

// Consulta para contar los jugadores con los filtros aplicados
$count_sql = "SELECT COUNT(*) FROM players";
$count_where_clauses = [];

if (!empty($search)) {
    $count_where_clauses[] = "(name LIKE ? OR last_name LIKE ? OR localidad LIKE ?)";
}

if ($categoria_filtro) {
    $count_where_clauses[] = "(SELECT c.name 
                         FROM categories c 
                         WHERE players.points >= c.min_points 
                         ORDER BY c.min_points DESC 
                         LIMIT 1) = ?";
}

if (!empty($count_where_clauses)) {
    $count_sql .= " WHERE " . implode(" AND ", $count_where_clauses);
}

$count_params = [];
$count_types = "";

// Agregar los parámetros de búsqueda y categoría
if (!empty($search)) {
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
    $count_types .= "sss";
}

if ($categoria_filtro) {
    $count_params[] = $categoria_filtro;
    $count_types .= "s";
}

// Ejecutar la consulta para contar los jugadores
if ($count_stmt = $conn->prepare($count_sql)) {
    if (!empty($count_params)) {
        $count_stmt->bind_param($count_types, ...$count_params);
    }
    $count_stmt->execute();
    $total_players = $count_stmt->get_result()->fetch_row()[0]; // Obtener el número total de jugadores filtrados
    $count_stmt->close();
}

// Calcular el número total de páginas
$total_pages = ceil($total_players / $results_per_page);


include 'functions/obtener_categoria.php';   
?>

<?php include 'layout/header.php'; ?>
<section class="ranking">
    <div class="container">
        <h1 class="title">Ranking Actual</h1>
        <?php if (isset($message)) echo "<p>$message</p>"; ?>
        <p class="cont_player">Total de Jugadores: <?php echo $total_players; ?></p>
        <div class="ranking__cont">
            <form method="GET" action="">
                <label for="categoria">Filtrar por categoría:</label>
                <div class="ranking_filter">
                    <select name="categoria" id="categoria">
                        <option value="">Todas las Categoría</option>
                        <option value="Primera" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Primera') echo 'selected'; ?>>Primera</option>
                        <option value="Segunda" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Segunda') echo 'selected'; ?>>Segunda</option>
                        <option value="Tercera" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Tercera') echo 'selected'; ?>>Tercera</option>
                        <option value="Cuarta" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Cuarta') echo 'selected'; ?>>Cuarta</option>
                        <option value="Quinta" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Quinta') echo 'selected'; ?>>Quinta</option>
                        <option value="Menores" <?php if (isset($_GET['categoria']) && $_GET['categoria'] == 'Menores') echo 'selected'; ?>>Menores</option>
                    </select>
                    <input type="submit" value="Filtrar">
                    <?php if (!empty($_GET['categoria'])): ?>
                        <a href="ranking.php" class="clear-filters-btn">Limpiar Filtro</a>
                    <?php endif; ?>
                </div>
            </form>

            <form method="GET" action="" class="search-form">
                <label for="categoria">Filtrar por Jugador o Localidad:</label>
                <div class="ranking_filter">
                    <input type="text" name="search" placeholder="Buscar jugador" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="submit" value="Buscar">
                    <?php if (!empty($_GET['search'])): ?>
                        <a href="ranking.php" class="clear-filters-btn">Limpiar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <table>
            <tr>
                <th>Posición</th>
                <th>Jugador</th>
                <th>Localidad</th>
                <th>Puntos</th>
                <th>Categoría</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                $position = $offset + 1;
                while ($row = $result->fetch_assoc()) {
                    $full_name = htmlspecialchars($row['last_name']);
                    if (isset($row['last_name'])) {
                        $full_name .= ' ' . htmlspecialchars($row['name']);
                    }

                    $categoria = obtener_categoria($row['points']);

                    echo "<tr>
                        <td>{$position}</td>
                        <td><a class='link_profile' href='" . url('/') . "players/player_profile.php?id={$row['id']}'>{$full_name}</a></td>
                        <td>{$row['localidad']}</td>
                        <td>{$row['points']}</td>
                        <td>{$categoria}</td>";

                    echo "</tr>";
                    $position++;
                }
            } else {
                echo "<tr><td colspan='5'>No hay jugadores registrados.</td></tr>";
            }
            ?>
        </table>

        <div class="pagination">
            <?php if ($total_players > $results_per_page): ?>
                <?php 
                // Construir la cadena de parámetros de búsqueda
                $query_params = [];
                if (!empty($search)) $query_params['search'] = urlencode($search);
                if (!empty($categoria_filtro)) $query_params['categoria'] = urlencode($categoria_filtro);
                $query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
                ?>
                
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1 . $query_string; ?>" class="<?php echo ($page == 1) ? 'disabled' : ''; ?>">Anterior</a>
                <?php endif; ?>

                Página <?php echo $page; ?> de <?php echo $total_pages; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1 . $query_string; ?>" class="<?php echo ($page == $total_pages) ? 'disabled' : ''; ?>">Siguiente</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>



    </div>
</section>
<?php $conn->close(); ?>
<?php include 'layout/footer.php'; ?>