<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/conexion.php'; // Conexión $conn

$mensaje = "";
$mostrar_form = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Buscar token válido
    $stmt = $conn->prepare("SELECT id, reset_expires FROM usuarios WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $reset_expires);
        $stmt->fetch();

        if (strtotime($reset_expires) > time()) {
            $mostrar_form = true;

            // Procesar formulario
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $password = $_POST['password'] ?? '';
                $password_confirm = $_POST['password_confirm'] ?? '';

                if ($password && $password === $password_confirm) {
                    if (strlen($password) >= 6) {
                        $pass_hash = password_hash($password, PASSWORD_DEFAULT);

                        // CORRECCIÓN: Usar 'password' (sin tilde) que es el nombre real en la BD
                        $update = $conn->prepare("UPDATE usuarios SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                        $update->bind_param("si", $pass_hash, $id);
                        
                        if ($update->execute()) {
                            if ($update->affected_rows > 0) {
                                $mensaje = "<div class='alert alert-success'>Contraseña actualizada correctamente. <a href='login.php'>Inicia sesión</a></div>";
                                $mostrar_form = false;
                            } else {
                                $mensaje = "<div class='alert alert-warning'>No se pudo actualizar la contraseña. Intenta nuevamente.</div>";
                            }
                        } else {
                            $mensaje = "<div class='alert alert-danger'>Error en la base de datos: " . $update->error . "</div>";
                        }
                        $update->close();
                    } else {
                        $mensaje = "<div class='alert alert-warning'>La contraseña debe tener al menos 6 caracteres.</div>";
                    }
                } else {
                    $mensaje = "<div class='alert alert-warning'>Las contraseñas no coinciden.</div>";
                }
            }

        } else {
            $mensaje = "<div class='alert alert-danger'>El enlace ha expirado.</div>";
        }

    } else {
        $mensaje = "<div class='alert alert-danger'>Token inválido.</div>";
    }

    $stmt->close();
} else {
    $mensaje = "<div class='alert alert-danger'>Token no proporcionado.</div>";
}

$conn->close();
?>