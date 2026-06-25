<?php
session_start();

require 'conexion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['email'] ?? '';
    $contraseña = $_POST['password'] ?? '';

    if ($correo && $contraseña) {
    // 1. AQUI DEBE DECIR ', rol' EN EL SELECT
    $stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        if (password_verify($contraseña, $usuario['password'])) {
            $_SESSION["usuario"] = $usuario["nombre"];
            $_SESSION["id_usuario"] = $usuario["id"];
            
            // 2. ESTA LÍNEA ES OBLIGATORIA
            $_SESSION["rol"] = $usuario["rol"]; 

            header("Location: index.php");
            exit;
        } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "Usuario no encontrado";
        }
        $stmt->close();
    } else {
        $error = "Por favor completa todos los campos";
    }
    
    // Si hay error, redirigir de vuelta al login con mensaje
    if ($error) {
        header("Location: login.php?error=" . urlencode($error));
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>