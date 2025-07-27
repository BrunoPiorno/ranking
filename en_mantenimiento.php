<?php
include('layout/header.php');
?>

<style>
    .maintenance-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 60vh;
        text-align: center;
    }
    .maintenance-box {
        border: 1px solid #ddd;
        padding: 40px;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .maintenance-box h1 {
        font-size: 2em;
        color: #333;
        margin-bottom: 20px;
    }
    .maintenance-box p {
        font-size: 1.2em;
        color: #666;
    }
</style>

<div class="maintenance-container">
    <div class="maintenance-box">
        <h1>Estamos trabajando en ello</h1>
        <p>Estamos actualizando la carga de datos del ranking. ¡En unos días estará listo!</p>
    </div>
</div>

<?php
include('layout/footer.php');
?>
