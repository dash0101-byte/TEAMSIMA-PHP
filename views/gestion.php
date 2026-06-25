<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "drondb");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_base_id = 1;
$mensaje = "";

/* AGREGAR USUARIO */
if (isset($_POST['agregar'])) {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $rol = $_POST['rol'];

    $password_segura = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $correo, $password_segura, $rol);
    $stmt->execute();

    header("Location: gestion.php");
    exit();
}

/* ACTUALIZAR USUARIO */
if (isset($_POST['actualizar'])) {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $rol = $_POST['rol'];
    $password = trim($_POST['password']);

    if ($id == $admin_base_id) {
        $rol = "admin";
    }

    if (isset($_SESSION['id']) && $id == $_SESSION['id'] && $rol !== "admin") {
        header("Location: gestion.php?error=protegido");
        exit();
    }

    if (!empty($password)) {
        $password_segura = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ?, password = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nombre, $correo, $password_segura, $rol, $id);
    } else {
        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $correo, $rol, $id);
    }

    $stmt->execute();

    if (isset($_SESSION['id']) && $id == $_SESSION['id']) {
        $_SESSION['usuario'] = $nombre;
        $_SESSION['rol'] = $rol;
    }

    header("Location: gestion.php");
    exit();
}

/* HACER ADMIN */
if (isset($_GET['hacer_admin'])) {
    $id = intval($_GET['hacer_admin']);
    $conexion->query("UPDATE usuarios SET rol = 'admin' WHERE id = $id");
    header("Location: gestion.php");
    exit();
}

/* QUITAR ADMIN */
if (isset($_GET['quitar_admin'])) {
    $id = intval($_GET['quitar_admin']);

    if ($id == $admin_base_id || (isset($_SESSION['id']) && $id == $_SESSION['id'])) {
        header("Location: gestion.php?error=protegido");
        exit();
    }

    $conexion->query("UPDATE usuarios SET rol = 'cliente' WHERE id = $id");
    header("Location: gestion.php");
    exit();
}

/* ELIMINAR */
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    if ($id == $admin_base_id || (isset($_SESSION['id']) && $id == $_SESSION['id'])) {
        header("Location: gestion.php?error=protegido");
        exit();
    }

    $conexion->query("DELETE FROM usuarios WHERE id = $id");
    header("Location: gestion.php");
    exit();
}

/* TRAER DATOS PARA EDITAR */
$usuario_editar = null;

if (isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $resultado_editar = $conexion->query("SELECT * FROM usuarios WHERE id = $id_editar");

    if ($resultado_editar && $resultado_editar->num_rows > 0) {
        $usuario_editar = $resultado_editar->fetch_assoc();
    }
}

$administradores = $conexion->query("SELECT * FROM usuarios WHERE rol = 'admin' ORDER BY id ASC");
$usuarios = $conexion->query("SELECT * FROM usuarios WHERE rol IN ('cliente', 'vista_previa') ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control | Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-primary: #0a0e1a;
            --bg-secondary: #111827;
            --bg-card: #1a2332;
            --bg-hover: #2d3a4f;
            --border-color: #2d3a4f;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --accent-blue: #3b82f6;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --accent-yellow: #f59e0b;
            --accent-purple: #8b5cf6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            position: relative;
        }

        /* Efecto de fondo animado */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(59,130,246,0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .contenedor {
            max-width: 1400px;
            width: 92%;
            margin: 0 auto;
            padding: 30px 0;
            position: relative;
            z-index: 1;
        }

        /* Header moderno */
        .header-modern {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 20px;
            background: rgba(26, 35, 50, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            border: 1px solid var(--border-color);
        }

        .header-modern h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, var(--accent-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .header-modern p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        /* Tarjetas neumórficas */
        .card-modern {
            background: var(--bg-card);
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            margin-bottom: 35px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
        }

        .card-header-modern {
            padding: 20px 25px;
            font-weight: 700;
            font-size: 1.3rem;
            background: linear-gradient(135deg, rgba(59,130,246,0.2) 0%, rgba(139,92,246,0.2) 100%);
            border-bottom: 2px solid var(--accent-blue);
            letter-spacing: -0.3px;
        }

        .card-header-modern i {
            margin-right: 12px;
            color: var(--accent-blue);
        }

        .card-body-modern {
            padding: 25px;
        }

        /* Formulario moderno */
        .form-group-modern {
            margin-bottom: 20px;
        }

        .form-group-modern label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control-modern, .form-select-modern {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.8);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus, .form-select-modern:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
            background: var(--bg-secondary);
        }

        .form-control-modern::placeholder {
            color: var(--text-secondary);
        }

        /* Tabla moderna */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 16px;
        }

        .table-modern {
            width: 100%;
            background: transparent;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table-modern thead th {
            padding: 15px 12px;
            background: rgba(59,130,246,0.15);
            color: var(--text-primary);
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            text-align: center;
        }

        .table-modern tbody tr {
            background: rgba(26, 35, 50, 0.6);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .table-modern tbody tr:hover {
            background: var(--bg-hover);
            transform: scale(1.01);
        }

        .table-modern tbody td {
            padding: 15px 12px;
            color: var(--text-primary);
            border: none;
            text-align: center;
            font-size: 0.9rem;
        }

        /* Botones modernos */
        .btn-modern {
            padding: 8px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            border: none;
            margin: 3px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .btn-modern i {
            font-size: 0.9rem;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            filter: brightness(110%);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--accent-blue), #2563eb);
            color: white;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, var(--accent-green), #059669);
            color: white;
        }

        .btn-warning-modern {
            background: linear-gradient(135deg, var(--accent-yellow), #d97706);
            color: white;
        }

        .btn-danger-modern {
            background: linear-gradient(135deg, var(--accent-red), #dc2626);
            color: white;
        }

        .btn-info-modern {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }

        .btn-secondary-modern {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }

        /* Badges modernos */
        .badge-modern {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(59,130,246,0.2);
            color: var(--accent-blue);
            border: 1px solid var(--accent-blue);
        }

        .badge-protected {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Mensaje de error */
        .alert-modern {
            background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(220,38,38,0.1));
            border-left: 4px solid var(--accent-red);
            color: #fca5a5;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            backdrop-filter: blur(10px);
        }

        /* Botón volver */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 12px 28px;
            font-size: 1rem;
            margin-top: 20px;
            background: rgba(107,114,128,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-modern, .header-modern {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Scrollbar personalizada */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-blue);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contenedor {
                width: 95%;
                padding: 20px 0;
            }
            
            .header-modern h1 {
                font-size: 1.8rem;
            }
            
            .btn-modern {
                padding: 6px 12px;
                font-size: 0.75rem;
            }
            
            .table-modern tbody td {
                font-size: 0.8rem;
                padding: 10px 8px;
            }
        }
    </style>
</head>

<body>

<div class="contenedor">

    <!-- Header moderno -->
    <div class="header-modern">
        <h1>
            <i class="fas fa-users-gear"></i> Panel de Control
        </h1>
        <p>Gestión avanzada de usuarios y administradores del sistema</p>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-modern">
            <i class="fas fa-shield-haltered"></i> 
            No puedes modificar, degradar ni eliminar el administrador base o tu propia cuenta.
        </div>
    <?php endif; ?>

    <!-- FORMULARIO AGREGAR / EDITAR -->
    <div class="card-modern">
        <div class="card-header-modern">
            <i class="fas <?php echo $usuario_editar ? 'fa-pen' : 'fa-user-plus'; ?>"></i>
            <?php echo $usuario_editar ? "Editar Usuario" : "Registrar Nuevo Usuario"; ?>
        </div>

        <div class="card-body-modern">
            <form method="POST" action="gestion.php">

                <?php if ($usuario_editar): ?>
                    <input type="hidden" name="id" value="<?php echo $usuario_editar['id']; ?>">
                <?php endif; ?>

                <div class="row g-3">

                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label><i class="fas fa-user"></i> Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control-modern" required
                                   value="<?php echo $usuario_editar ? htmlspecialchars($usuario_editar['nombre']) : ''; ?>"
                                   placeholder="Ingrese el nombre">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control-modern" required
                                   value="<?php echo $usuario_editar ? htmlspecialchars($usuario_editar['correo']) : ''; ?>"
                                   placeholder="usuario@ejemplo.com">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label><i class="fas fa-lock"></i> Contraseña</label>
                            <input type="password" name="password" class="form-control-modern"
                                   <?php echo $usuario_editar ? '' : 'required'; ?>
                                   placeholder="<?php echo $usuario_editar ? 'Dejar vacío para mantener' : 'Ingrese contraseña'; ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label><i class="fas fa-tag"></i> Rol de Usuario</label>
                            <select name="rol" class="form-select-modern" required
                                <?php echo ($usuario_editar && $usuario_editar['id'] == $admin_base_id) ? 'disabled' : ''; ?>>

                                <option value="cliente"
                                    <?php echo ($usuario_editar && $usuario_editar['rol'] == 'cliente') ? 'selected' : ''; ?>>
                                    🧑 Cliente
                                </option>

                                <option value="vista_previa"
                                    <?php echo ($usuario_editar && $usuario_editar['rol'] == 'vista_previa') ? 'selected' : ''; ?>>
                                    👁️ Vista previa
                                </option>

                                <option value="admin"
                                    <?php echo ($usuario_editar && $usuario_editar['rol'] == 'admin') ? 'selected' : ''; ?>>
                                    👑 Administrador
                                </option>

                            </select>

                            <?php if ($usuario_editar && $usuario_editar['id'] == $admin_base_id): ?>
                                <input type="hidden" name="rol" value="admin">
                                <small class="text-info" style="display: block; margin-top: 5px;">
                                    <i class="fas fa-info-circle"></i> Rol protegido del administrador base
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <div class="mt-4">
                    <?php if ($usuario_editar): ?>
                        <button type="submit" name="actualizar" class="btn-modern btn-warning-modern">
                            <i class="fas fa-save"></i> Actualizar Usuario
                        </button>

                        <a href="gestion.php" class="btn-modern btn-secondary-modern">
                            <i class="fas fa-times"></i> Cancelar Edición
                        </a>
                    <?php else: ?>
                        <button type="submit" name="agregar" class="btn-modern btn-success-modern">
                            <i class="fas fa-plus-circle"></i> Agregar Usuario
                        </button>
                    <?php endif; ?>
                </div>

            </form>
        </div>
    </div>

    <!-- TABLA ADMINISTRADORES -->
    <div class="card-modern">
        <div class="card-header-modern">
            <i class="fas fa-shield-haltered"></i>
            Administradores del Sistema
        </div>

        <div class="card-body-modern">
            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user"></i> Nombre</th>
                            <th><i class="fas fa-envelope"></i> Correo</th>
                            <th><i class="fas fa-tag"></i> Rol</th>
                            <th><i class="fas fa-calendar"></i> Registro</th>
                            <th><i class="fas fa-cogs"></i> Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($admin = $administradores->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo $admin['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($admin['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($admin['correo']); ?></td>
                                <td><span class="badge-modern"><i class="fas fa-crown"></i> <?php echo $admin['rol']; ?></span></td>
                                <td><?php echo $admin['fecha_registro']; ?></td>
                                <td>

                                    <a href="gestion.php?editar=<?php echo $admin['id']; ?>"
                                       class="btn-modern btn-info-modern">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>

                                    <?php if ($admin['id'] == $admin_base_id): ?>

                                        <span class="badge-protected">
                                            <i class="fas fa-lock"></i> Admin Base Protegido
                                        </span>

                                    <?php else: ?>

                                        <a href="gestion.php?quitar_admin=<?php echo $admin['id']; ?>"
                                           class="btn-modern btn-warning-modern"
                                           onclick="return confirm('⚠️ ¿Seguro que quieres quitarle permisos de administrador?')">
                                            <i class="fas fa-user-minus"></i> Quitar Admin
                                        </a>

                                        <a href="gestion.php?eliminar=<?php echo $admin['id']; ?>"
                                           class="btn-modern btn-danger-modern"
                                           onclick="return confirm('⚠️ ¿Seguro que quieres eliminar este administrador? Esta acción no se puede deshacer.')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>

                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- TABLA USUARIOS NORMALES -->
    <div class="card-modern">
        <div class="card-header-modern">
            <i class="fas fa-users"></i>
            Usuarios Estándar
        </div>

        <div class="card-body-modern">
            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user"></i> Nombre</th>
                            <th><i class="fas fa-envelope"></i> Correo</th>
                            <th><i class="fas fa-tag"></i> Rol</th>
                            <th><i class="fas fa-calendar"></i> Registro</th>
                            <th><i class="fas fa-cogs"></i> Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo $usuario['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                <td>
                                    <?php if($usuario['rol'] == 'cliente'): ?>
                                        <span class="badge-modern"><i class="fas fa-user"></i> Cliente</span>
                                    <?php else: ?>
                                        <span class="badge-modern"><i class="fas fa-eye"></i> Vista Previa</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $usuario['fecha_registro']; ?></td>
                                <td>

                                    <a href="gestion.php?editar=<?php echo $usuario['id']; ?>"
                                       class="btn-modern btn-info-modern">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>

                                    <a href="gestion.php?hacer_admin=<?php echo $usuario['id']; ?>"
                                       class="btn-modern btn-primary-modern"
                                       onclick="return confirm('👑 ¿Quieres convertir este usuario en administrador?')">
                                        <i class="fas fa-crown"></i> Hacer Admin
                                    </a>

                                    <a href="gestion.php?eliminar=<?php echo $usuario['id']; ?>"
                                       class="btn-modern btn-danger-modern"
                                       onclick="return confirm('⚠️ ¿Seguro que quieres eliminar este usuario?')">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div style="text-align: center;">
        <a href="dash2.php" class="btn-modern btn-secondary-modern btn-back">
            <i class="fas fa-arrow-left"></i>
            Volver al Dashboard
        </a>
    </div>

</div>

</body>
</html>
