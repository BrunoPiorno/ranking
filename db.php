<?php
$servername = "localhost";
$username = "root"; // Cambia esto si es necesario
$password = "root"; // Cambia esto si es necesario
$dbname = "ranking"; // Usa la base de datos que creaste

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
