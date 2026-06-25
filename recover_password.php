<?php
session_start();
require 'conexion.php'; // tu conexión a la BD
$mensaje = ""; // Inicializamos la variable

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['email'] ?? '';
    $respuesta = $_POST['respuesta'] ?? '';
    $nueva = $_POST['nueva_password'] ?? '';

    if ($correo && $respuesta && $nueva) {
        // Primero obtenemos el usuario y su respuesta hasheada
        $stmt = $conn->prepare("SELECT id, respuesta_secreta FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            
            // Verificamos la respuesta usando password_verify
            if (password_verify($respuesta, $usuario['respuesta_secreta'])) {
                // Actualizamos la contraseña
                $hashed = password_hash($nueva, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE usuarios SET contraseña = ? WHERE correo = ?");
                $update->bind_param("ss", $hashed, $correo);
                
                if ($update->execute()) {
                    $mensaje = "success:Contraseña actualizada correctamente. <a href='login.php'>Inicia sesión</a>";
                } else {
                    $mensaje = "error:Error al actualizar la contraseña.";
                }
                $update->close();
            } else {
                $mensaje = "error:Correo o respuesta incorrectos.";
            }
        } else {
            $mensaje = "error:Correo o respuesta incorrectos.";
        }
        $stmt->close();
    } else {
        $mensaje = "error:Completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Dron Magnético</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2c3e50;
            --accent: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --danger: #e74c3c;
            --success: #27ae60;
            --warning: #f39c12;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .main-container {
            width: 100%;
            max-width: 450px;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--accent), var(--primary));
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--secondary), var(--dark));
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--accent);
        }
        
        .card-header h2 {
            font-size: 1.8rem;
            margin: 0;
            font-weight: 600;
        }
        
        .card-header p {
            opacity: 0.9;
            margin-top: 8px;
            font-size: 0.95rem;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success);
            border-left-color: var(--success);
        }
        
        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger);
            border-left-color: var(--danger);
        }
        
        .alert i {
            font-size: 1.3rem;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            background: #f8f9fa;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            background: white;
            box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.1);
        }
        
        .form-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
            font-size: 1.1rem;
            transition: var(--transition);
        }
        
        .form-control:focus + .form-icon {
            color: var(--accent);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--accent), var(--primary));
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .password-strength {
            margin-top: 8px;
        }
        
        .strength-bar {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: var(--transition);
        }
        
        .strength-weak { background: var(--danger); width: 33%; }
        .strength-medium { background: var(--warning); width: 66%; }
        .strength-strong { background: var(--success); width: 100%; }
        
        .card-footer {
            padding: 20px 30px;
            text-align: center;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }
        
        .back-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }
        
        .back-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .card-header, .card-body {
                padding: 25px 20px;
            }
            
            .card-header h2 {
                font-size: 1.6rem;
            }
            
            .card-icon {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-key card-icon"></i>
                <h2>Recuperar Contraseña</h2>
                <p>Ingresa tus datos para restablecer tu contraseña</p>
            </div>
            
            <div class="card-body">
                <?php 
                if(!empty($mensaje)): 
                    list($type, $message) = explode(':', $mensaje, 2);
                ?>
                    <?php if($type === 'success'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <div><?= $message ?></div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <div><?= $message ?></div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <i class="fas fa-envelope form-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-shield-alt form-icon"></i>
                        <input type="text" name="respuesta" class="form-control" placeholder="Respuesta secreta" required>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-lock form-icon"></i>
                        <input type="password" name="nueva_password" class="form-control" placeholder="Nueva contraseña" id="nueva_password" required>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strength-fill"></div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-redo-alt"></i> Actualizar Contraseña
                    </button>
                </form>
            </div>
            
            <div class="card-footer">
                <a href="login.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio de Sesión
                </a>
            </div>
        </div>
    </div>

    <script>
        // Indicador de fuerza de contraseña
        document.getElementById('nueva_password').addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('strength-fill');
            let strength = 0;
            
            if (password.length >= 6) strength += 1;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
            if (password.match(/\d/)) strength += 1;
            if (password.match(/[^a-zA-Z\d]/)) strength += 1;
            
            strengthFill.className = 'strength-fill';
            if (strength === 1) strengthFill.classList.add('strength-weak');
            else if (strength === 2) strengthFill.classList.add('strength-medium');
            else if (strength >= 3) strengthFill.classList.add('strength-strong');
        });
    </script>
</body>
</html>