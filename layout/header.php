<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking de Tenis de Mesa Trenque Lauquen</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header">
                <img src="images/logo-posta.png" alt="Logo" id="logo">
                <div class="header__cont">
                    <h1>Ranking de Tenis de Mesa Trenque Lauquen</h1>
                    <nav>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="form_add_player.php">Registrar Jugador</a>
                            <a href="add_match.php">Registrar Partido</a>
                        <?php endif; ?>
                        <a href="ranking.php">Ver Ranking</a>
                        <a href="como_funciona.php">Sistema de Puntos</a>
                    </nav>
                </div>

                <div class="header__log">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
                    <?php else: ?>
                        <a href="login.php" class="login-btn">Iniciar Sesión</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </header>
</body>
</html>
