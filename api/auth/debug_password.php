<?php
session_start();
require_once '../../config/conexion.php';

// Verificar un usuario específico
$email = "tu_correo@ejemplo.com"; // Cambia por un email que exista

$stmt = $conn->prepare("SELECT id, nombre, correo, contraseña FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    echo "<h3>Debug Usuario:</h3>";
    echo "ID: " . $usuario['id'] . "<br>";
    echo "Nombre: " . $usuario['nombre'] . "<br>";
    echo "Correo: " . $usuario['correo'] . "<br>";
    echo "Contraseña (hash): " . $usuario['contraseña'] . "<br>";
    echo "Longitud hash: " . strlen($usuario['contraseña']) . "<br>";
} else {
    echo "Usuario no encontrado";
}
$stmt->close();
$conn->close();
?>
