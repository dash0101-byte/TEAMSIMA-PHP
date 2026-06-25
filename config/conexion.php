<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "drondb";

try {
    // La cadena se arma dinámicamente con tus datos de XAMPP
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    // Modo de errores estrictos (lanza excepciones como los try/catch de Node.js)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Si falla, responde en JSON para que JavaScript no truene con un error de HTML
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Error de conexión a drondb: " . $e->getMessage()
    ]);
    die();
}
?>