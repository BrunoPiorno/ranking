<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
require_once __DIR__ . '/../config.php';?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ranking de Tenis de Mesa Trenque Lauquen. Consulta los puntos y posiciones de los jugadores.">
    <meta name="keywords" content="tenis de mesa, ranking, jugadores, puntos, torneo">
    <meta name="author" content="Tu Nombre o Nombre de la Organización">
    <title>Ranking de Tenis de Mesa Trenque Lauquen</title>
    <link rel="icon" href="<?= url('/') ?>images/logo-posta.png" type="image/x-icon"> 
    <link rel="stylesheet" href="<?= url('/') ?>css/styles.css">
    <link rel="stylesheet" href="<?= url('/') ?>css/player_profile.css">
    <link rel="stylesheet" href="<?= url('/') ?>css/tournament.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-R9X5XTTZ8D"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-R9X5XTTZ8D');
    </script>
</head>
<body>
    <header>
        <div class="container">
            <div class="header">
                <a href="<?= url('/') ?>"><img src="<?= url('/') ?>images/logo-posta.png" alt="Logo" id="logo"></a>
                <div class="header__cont">
                    <h1>Ranking de Tenis de Mesa Trenque Lauquen</h1>
                    <nav>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?= url('/') ?>players/add_player.php">Registrar Jugador</a>
                            <a href="<?= url('/') ?>add_match.php">Registrar Partido</a>
                        <?php endif; ?>
                        <a href="<?= url('/') ?>ranking.php">Ver Ranking</a>
                        <a href="<?= url('/') ?>tournament/tournament.php">Podios</a>
                        <a href="<?= url('/') ?>como_funciona.php">Sistema de Puntos</a>
                    </nav>
                </div>

                <div class="header__log">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= url('/') ?>auth/logout.php" class="logout-btn">Cerrar Sesión</a>
                    <?php else: ?>
                        <a href="<?= url('/') ?>auth/login.php" class="login-btn">Iniciar Sesión</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
</body>
</html>
