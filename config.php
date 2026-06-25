<?php
// Configuración de la base de datos
$host = "localhost";
$usuario = "root";      // Tu usuario de MySQL
$password = "";          // Tu contraseña de MySQL (XAMPP normalmente vacío)
$basedatos = "drondb";   // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $password, $basedatos);

// Revisar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8");
?>
