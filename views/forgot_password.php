<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/conexion.php';

$mensaje = "";
$mostrar_formulario_recuperacion = true;
$mostrar_formulario_password = false;
$usuario_id = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Si se está enviando el formulario de nueva contraseña
    if (isset($_POST['nueva_password'])) {
        $nueva_password = $_POST['nueva_password'] ?? '';
        $confirmar_password = $_POST['confirmar_password'] ?? '';
        $usuario_id = $_POST['usuario_id'] ?? '';
        
        if ($nueva_password && $confirmar_password && $usuario_id) {
            if ($nueva_password === $confirmar_password) {
                if (strlen($nueva_password) >= 6) {
                    $pass_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                    
                    // Actualizar la contraseña directamente
                    $update = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                    $update->bind_param("si", $pass_hash, $usuario_id);
                    
                    if ($update->execute()) {
                        $mensaje = "<div class='alert alert-success'>
                                    <i class='fas fa-check-circle'></i>
                                    <strong>¡Contraseña actualizada correctamente!</strong><br>
                                    Ahora puedes iniciar sesión con tu nueva contraseña.
                                    </div>";
                        $mostrar_formulario_recuperacion = false;
                        $mostrar_formulario_password = false;
                    } else {
                        $mensaje = "<div class='alert alert-danger'>Error al actualizar la contraseña.</div>";
                    }
                    $update->close();
                } else {
                    $mensaje = "<div class='alert alert-warning'>La contraseña debe tener al menos 6 caracteres.</div>";
                    $mostrar_formulario_password = true;
                }
            } else {
                $mensaje = "<div class='alert alert-warning'>Las contraseñas no coinciden.</div>";
                $mostrar_formulario_password = true;
            }
        }
    } 
    // Si se está enviando el formulario de validación
    else {
        $email = $_POST['email'] ?? '';
        $pregunta = $_POST['pregunta'] ?? '';
        $respuesta = $_POST['respuesta'] ?? '';

        if ($email && $pregunta && $respuesta) {
            // Verificar usuario, pregunta y respuesta
            $stmt = $conn->prepare("SELECT id, respuesta_secreta FROM usuarios WHERE correo = ? AND pregunta_secreta = ?");
            $stmt->bind_param("ss", $email, $pregunta);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $usuario = $result->fetch_assoc();
                
                // Verificar respuesta secreta
                if (password_verify($respuesta, $usuario['respuesta_secreta'])) {
                    $mensaje = "<div class='alert alert-success'>
                                <i class='fas fa-check-circle'></i>
                                <strong>¡Validación exitosa!</strong><br>
                                Ahora puedes establecer tu nueva contraseña.
                                </div>";
                    $mostrar_formulario_recuperacion = false;
                    $mostrar_formulario_password = true;
                    $usuario_id = $usuario['id'];
                } else {
                    $mensaje = "<div class='alert alert-danger'>
                                <i class='fas fa-exclamation-circle'></i>
                                La respuesta secreta es incorrecta.
                                </div>";
                }
            } else {
                $mensaje = "<div class='alert alert-danger'>
                            <i class='fas fa-exclamation-circle'></i>
                            No se encontró usuario con esos datos.
                            </div>";
            }
            $stmt->close();
        } else {
            $mensaje = "<div class='alert alert-warning'>
                        <i class='fas fa-exclamation-triangle'></i>
                        Todos los campos son obligatorios.
                        </div>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    position: relative;
    overflow-x: hidden;
}

/* Efecto de partículas sutiles en el fondo */
body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
    pointer-events: none;
}

.recovery-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.2);
    max-width: 500px;
    width: 100%;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.3);
    animation: cardEntrance 0.6s ease-out;
    position: relative;
    z-index: 1;
}

/* Animación de entrada suave */
@keyframes cardEntrance {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px 25px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Efecto de brillo sutil en el header */
.card-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent 0%,
        rgba(255, 255, 255, 0.1) 50%,
        transparent 100%
    );
    transform: rotate(45deg);
    animation: shine 3s infinite linear;
}

@keyframes shine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.card-header h3 {
    margin: 0;
    font-size: 1.9rem;
    font-weight: 700;
    position: relative;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    letter-spacing: -0.5px;
}

.card-header p {
    margin: 12px 0 0 0;
    opacity: 0.95;
    font-size: 1rem;
    font-weight: 400;
    position: relative;
}

.card-body {
    padding: 35px 30px;
    position: relative;
}

.alert {
    border-radius: 12px;
    border: none;
    padding: 18px 20px;
    margin-bottom: 25px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.alert:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}

.alert::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    border-radius: 12px 0 0 12px;
}

.alert i {
    font-size: 1.3rem;
    margin-top: 2px;
    flex-shrink: 0;
    width: 24px;
    text-align: center;
}

.alert-success {
    background: linear-gradient(135deg, rgba(46, 204, 113, 0.08) 0%, rgba(39, 174, 96, 0.12) 100%);
    color: #216e3e;
    border-left: 4px solid #27ae60;
}

.alert-success::before {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
}

.alert-danger {
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.08) 0%, rgba(192, 57, 43, 0.12) 100%);
    color: #7c2c27;
    border-left: 4px solid #c0392b;
}

.alert-danger::before {
    background: linear-gradient(135deg, #c0392b, #e74c3c);
}

.alert-warning {
    background: linear-gradient(135deg, rgba(243, 156, 18, 0.08) 0%, rgba(211, 84, 0, 0.12) 100%);
    color: #8a4e0f;
    border-left: 4px solid #d35400;
}

.alert-warning::before {
    background: linear-gradient(135deg, #d35400, #f39c12);
}

.form-control {
    padding: 14px 16px;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 1rem;
    background: #fafafa;
    font-weight: 500;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 
        0 0 0 3px rgba(102, 126, 234, 0.15),
        0 4px 12px rgba(102, 126, 234, 0.1);
    outline: none;
    background: white;
    transform: translateY(-1px);
}

.form-control::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

.btn-recovery {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1.05rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
}

.btn-recovery::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s;
}

.btn-recovery:hover::before {
    left: 100%;
}

.btn-recovery:hover {
    transform: translateY(-3px);
    box-shadow: 
        0 8px 25px rgba(102, 126, 234, 0.35),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    color: white;
}

.btn-recovery:active {
    transform: translateY(-1px);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
}

.password-strength {
    height: 6px;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-radius: 10px;
    margin-top: 10px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.strength-meter {
    height: 100%;
    width: 0;
    border-radius: 10px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.strength-meter::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    background-size: 20px 100%;
    animation: shine 1.5s infinite;
}

@keyframes shine {
    0% { transform: translateX(-20px); }
    100% { transform: translateX(20px); }
}

.strength-weak {
    width: 33%;
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

.strength-medium {
    width: 66%;
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}

.strength-strong {
    width: 100%;
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.8; }
    100% { opacity: 1; }
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    padding: 10px 16px;
    border-radius: 8px;
    background: rgba(102, 126, 234, 0.08);
    border: 1px solid rgba(102, 126, 234, 0.1);
}

.back-link:hover {
    color: #764ba2;
    background: rgba(102, 126, 234, 0.15);
    text-decoration: none;
    transform: translateX(-5px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

/* Efectos de enfoque mejorados para inputs */
.input-group {
    position: relative;
}

.input-group .form-control {
    padding-right: 45px;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    z-index: 5;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: #667eea;
}

/* Responsive improvements */
@media (max-width: 576px) {
    body {
        padding: 15px;
    }
    
    .recovery-card {
        border-radius: 16px;
    }
    
    .card-header {
        padding: 25px 20px;
    }
    
    .card-header h3 {
        font-size: 1.6rem;
    }
    
    .card-body {
        padding: 25px 20px;
    }
    
    .alert {
        padding: 15px;
        gap: 12px;
    }
}

/* Estados de carga */
.btn-recovery.loading {
    pointer-events: none;
}

.btn-recovery.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid transparent;
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
    </style>
</head>
<body>
    <div class="recovery-card">
        <div class="card-header">
            <h3><i class="fas fa-key"></i> Recuperar Contraseña</h3>
            <p class="mb-0">Restablece tu contraseña de forma segura</p>
        </div>
        
        <div class="card-body">
            <?= $mensaje ?>
            
            <!-- Formulario de validación (pregunta secreta) -->
            <?php if ($mostrar_formulario_recuperacion): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Ingresa tu correo electrónico" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pregunta" class="form-label">Pregunta de Seguridad</label>
                        <select id="pregunta" name="pregunta" class="form-control" required>
                            <option value="">Selecciona una pregunta secreta</option>
                            <option value="¿Cuál es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                            <option value="¿Cuál es tu ciudad de nacimiento?">¿Cuál es tu ciudad de nacimiento?</option>
                            <option value="¿Cuál es tu comida favorita?">¿Cuál es tu comida favorita?</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="respuesta" class="form-label">Respuesta Secreta</label>
                        <input type="text" id="respuesta" name="respuesta" class="form-control" placeholder="Tu respuesta secreta" required>
                    </div>
                    
                    <button type="submit" class="btn-recovery">
                        <i class="fas fa-shield-alt"></i> Validar Identidad
                    </button>
                </form>
            <?php endif; ?>
            
            <!-- Formulario de nueva contraseña -->
            <?php if ($mostrar_formulario_password): ?>
                <form method="POST">
                    <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                    
                    <div class="mb-3">
                        <label for="nueva_password" class="form-label">Nueva Contraseña</label>
                        <input type="password" id="nueva_password" name="nueva_password" class="form-control" placeholder="Ingresa tu nueva contraseña" required minlength="6">
                        <div class="password-strength">
                            <div class="strength-meter" id="password-strength-meter"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmar_password" class="form-label">Confirmar Contraseña</label>
                        <input type="password" id="confirmar_password" name="confirmar_password" class="form-control" placeholder="Confirma tu nueva contraseña" required minlength="6">
                    </div>
                    
                    <button type="submit" class="btn-recovery mb-2">
                        <i class="fas fa-save"></i> Guardar Nueva Contraseña
                    </button>
                    
                    <button type="button" class="btn-secondary" onclick="window.location.href='forgot_password.php'">
                        <i class="fas fa-undo"></i> Volver a Validar
                    </button>
                </form>
            <?php endif; ?>
            
            <!-- Enlace para volver al login -->
            <?php if (!$mostrar_formulario_recuperacion && !$mostrar_formulario_password): ?>
                <div class="text-center mt-3">
                    <a href="login.php" class="btn-recovery">
                        <i class="fas fa-sign-in-alt"></i> Ir al Inicio de Sesión
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center mt-3">
                    <a href="login.php" class="back-link">
                        <i class="fas fa-arrow-left"></i> Volver al Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Validación de fortaleza de contraseña
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('nueva_password');
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    const meter = document.getElementById('password-strength-meter');
                    
                    // Resetear clases
                    meter.className = 'strength-meter';
                    
                    if (password.length === 0) {
                        return;
                    }
                    
                    // Calcular fortaleza
                    let strength = 0;
                    
                    // Longitud
                    if (password.length >= 8) strength += 1;
                    
                    // Caracteres variados
                    if (/[a-z]/.test(password)) strength += 1;
                    if (/[A-Z]/.test(password)) strength += 1;
                    if (/[0-9]/.test(password)) strength += 1;
                    if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
                    
                    // Aplicar clases según fortaleza
                    if (strength <= 2) {
                        meter.classList.add('strength-weak');
                    } else if (strength <= 4) {
                        meter.classList.add('strength-medium');
                    } else {
                        meter.classList.add('strength-strong');
                    }
                });
            }
        });
    </script>
</body>
</html>