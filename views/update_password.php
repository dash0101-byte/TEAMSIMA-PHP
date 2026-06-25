<?php
session_start();
require_once '../../config/conexion.php';

if(isset($_POST['token'], $_POST['new_password'], $_POST['confirm_password'])){
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if($new_password !== $confirm_password) die("Las contraseñas no coinciden");

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE reset_token=? AND reset_expires > NOW()");
    $stmt->bind_param("s",$token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE usuarios SET contraseña=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
        $stmt->bind_param("si",$hashed,$user['id']);
        $stmt->execute();

        echo "<div class='alert alert-success text-center'>Contraseña actualizada. <a href='login.php'>Inicia sesión</a></div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Token inválido o expirado.</div>";
    }
}
?>
