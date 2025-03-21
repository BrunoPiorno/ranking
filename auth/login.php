<?php
session_start();
include '../db.php';

$errorMessage = ""; // Variable para almacenar mensajes de error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar si el usuario existe en la base de datos
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Iniciar sesión y almacenar la información del usuario
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: ../ranking.php"); 
            exit;
        } else {
            $errorMessage = "Contraseña incorrecta.";
        }
    } else {
        $errorMessage = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <?php include '../layout/header.php'; ?>
    <section class="login">
        <div class="login-container">
            <h2 class="login-title">Iniciar Sesión</h2>   

            <form action="login.php" method="POST" class="login-form">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Usuario" required class="login-input">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Contraseña" required class="login-input">
                </div>

                <?php if ($errorMessage): ?>
                    <div class="error-message">
                        <p><?php echo $errorMessage; ?></p>
                    </div>
                <?php endif; ?>
                
                <button type="submit" class="login-button">Ingresar</button>
            </form>
        </div>
    </section>

    <?php include '../layout/footer.php'; ?>
</body>
</html>
