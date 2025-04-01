<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';


// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category_id']) && isset($_POST['min_points'])) {
        $category_id = (int)$_POST['category_id'];
        $min_points = (int)$_POST['min_points'];
        
        $stmt = $conn->prepare("UPDATE categories SET min_points = ? WHERE id = ?");
        $stmt->bind_param("ii", $min_points, $category_id);
        
        if ($stmt->execute()) {
            $message = "Categoría actualizada exitosamente.";
        } else {
            $message = "Error al actualizar la categoría.";
        }
        $stmt->close();
    }
}

// Obtener todas las categorías
$categories = [];
$result = $conn->query("SELECT * FROM categories ORDER BY min_points DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    $result->free();
}
?>

<?php include('layout/header.php');?>
    <!-- Add SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="container">
        <h1 class="title">Administrar Categorías</h1>
        
        <?php if (isset($message)): ?>
        <script>
            Swal.fire({
                title: '¡Éxito!',
                text: '<?php echo $message; ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
        <?php endif; ?>

        <table class="categories-table">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Puntos Mínimos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td>
                        <form method="POST" class="inline-form categories-form">
                            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                            <input type="number" name="min_points" value="<?php echo $category['min_points']; ?>" required>
                            <input type="submit" value="Actualizar">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="back-button">
            <a href="ranking.php" >Volver al Ranking</a>
        </div>
    </div>
<?php include('layout/footer.php');?>
