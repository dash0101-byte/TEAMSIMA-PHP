<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$rol = $_SESSION['rol'] ?? '';
$logueado = isset($_SESSION["usuario"]);
$puede_ver_sistema = ($rol === 'admin' || $rol === 'cliente');
$es_admin = ($rol === 'admin');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>SIMA</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

   <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --accent: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --danger: #e74c3c;
            --danger-dark: #c0392b;
            --warning: #e67e22;
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
        }

        .bob-navbar {
            background: linear-gradient(135deg, var(--secondary), var(--dark));
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            min-height: 80px;
            display: flex;
            align-items: center;
        }

        .bob-container {
            padding: 15px 30px;
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            flex-wrap: wrap;
        }

        .bob-logo h1 {
            margin: 0;
            font-size: 2.2rem;
            color: var(--light);
            font-weight: 700;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bob-logo h1 i {
            color: var(--accent);
            font-size: 2.5rem;
        }

        .bob-navbar-menu {
            flex: 1;
            display: flex;
            justify-content: center; /* CAMBIADO: centrado */
        }

        .bob-navbar-menu ul {
            list-style: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
        }

        .menu-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: rgba(255,255,255,0.1);
            color: var(--light);
            padding: 12px 20px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 1rem;
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid rgba(255,255,255,0.2);
            white-space: nowrap;
        }

        .menu-btn:hover {
            background-color: var(--accent);
            color: white;
            transform: translateY(-3px);
        }

        .admin-btn {
            background: linear-gradient(135deg, #e67e22, #d35400);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(230,126,34,0.4);
        }

        .admin-btn:hover {
            background: linear-gradient(135deg, #d35400, #c0392b);
            transform: translateY(-3px);
        }

        .vista-btn {
            background-color: rgba(255,255,255,0.08);
            opacity: 0.95;
        }

        .user-welcome {
            color: var(--light);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50px;
            white-space: nowrap;
        }

        .rol-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 20px;
            background: rgba(26,188,156,0.2);
            color: #7fffe2;
            border: 1px solid rgba(26,188,156,0.4);
            text-transform: uppercase;
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: var(--danger);
            color: white;
            padding: 10px 18px;
            border-radius: 50px;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
            white-space: nowrap;
        }

        .logout-btn:hover {
            background-color: var(--danger-dark);
            transform: translateY(-2px);
        }

        /* Menú hamburguesa para móvil */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            padding: 10px;
        }

        /* ============================================ */
        /* PANTALLAS GRANDES - Menú centrado */
        /* ============================================ */
        @media (min-width: 1101px) {
            .bob-container {
                display: grid;
                grid-template-columns: auto 1fr auto;
                align-items: center;
            }
            
            .bob-logo {
                justify-self: start;
            }
            
            .bob-navbar-menu {
                justify-self: center;
                width: auto;
                flex: none;
            }
            
            /* Elemento fantasma para balancear y mantener el menú centrado */
            .bob-container::after {
                content: '';
                width: 150px;
                visibility: hidden;
            }
        }

        /* ============================================ */
        /* PANTALLAS MEDIANAS (Tablet) */
        /* ============================================ */
        @media (max-width: 1100px) {
            .menu-toggle {
                display: block;
            }
            
            .bob-navbar-menu {
                display: none;
                width: 100%;
                order: 3;
                margin-top: 15px;
                justify-content: center;
            }
            
            .bob-navbar-menu.active {
                display: block;
            }
            
            .bob-navbar-menu ul {
                flex-direction: column;
                align-items: stretch;
            }
            
            .bob-navbar-menu ul li {
                width: 100%;
                text-align: center;
            }
            
            .menu-btn, .logout-btn, .user-welcome {
                width: 100%;
                justify-content: center;
            }
            
            .bob-container::after {
                display: none;
            }
        }

        /* ============================================ */
        /* PANTALLAS PEQUEÑAS (Móvil) */
        /* ============================================ */
        @media (max-width: 768px) {
            .bob-container {
                padding: 15px;
            }

            .bob-logo h1 {
                font-size: 1.8rem;
            }

            .bob-logo h1 i {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>

<header class="bob-navbar">
    <div class="bob-container">
        
        <div class="bob-logo">
            <h1>
                <i class="fas fa-drone"></i>
                SIMA
            </h1>
        </div>

        <!-- Botón menú hamburguesa -->
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <nav class="bob-navbar-menu" id="mainMenu">
            <ul>
                <!-- INICIO - SIEMPRE VISIBLE -->
                <li>
                    <a href="index.php" class="menu-btn">
                        <i class="fas fa-home"></i>
                        Inicio
                    </a>
                </li>

                <!-- MENÚ PARA USUARIOS LOGUEADOS (admin o cliente) -->
                <?php if ($puede_ver_sistema): ?>
                    <li>
                        <a href="dash2.php" class="menu-btn">
                            <i class="fas fa-chart-bar"></i>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="vuelos.php" class="menu-btn">
                            <i class="fas fa-plane"></i>
                            Registro de Vuelos
                        </a>
                    </li>

                    <li>
                        <a href="mapa_gps.php" class="menu-btn">
                            <i class="fas fa-map-marked-alt"></i>
                            Mapa GPS
                        </a>
                    </li>
                <?php endif; ?>

                <!-- COMENTARIOS - SIEMPRE VISIBLE -->
                <li>
                    <a href="comentarios.php" class="menu-btn">
                        <i class="fas fa-comments"></i>
                        Comentarios
                    </a>
                </li>

                <!-- USUARIO LOGUEADO -->
                <?php if ($logueado): ?>
                    <li>
                        <span class="user-welcome">
                            <i class="fas fa-user"></i>
                            Hola, <?= htmlspecialchars($_SESSION["usuario"]); ?>
                            <?php if (!empty($rol)): ?>
                                <span class="rol-badge">
                                    <?= htmlspecialchars($rol); ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </li>

                    <!-- GESTIÓN - SOLO ADMIN -->
                    <?php if ($es_admin): ?>
                        <li>
                            <a href="gestion.php" class="menu-btn admin-btn">
                                <i class="fas fa-users-cog"></i>
                                Gestión
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- SALIR -->
                    <li>
                        <a href="logout.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Salir
                        </a>
                    </li>

                <!-- USUARIO NO LOGUEADO -->
                <?php else: ?>
                    <li>
                        <a href="login.php" class="menu-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            Ingresar
                        </a>
                    </li>

                    <li>
                        <a href="registro.php" class="menu-btn" style="background-color:var(--accent); color:white;">
                            <i class="fas fa-user-plus"></i>
                            Registro
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<!-- JavaScript para el menú hamburguesa -->
<script>
    const menuToggle = document.getElementById('menuToggle');
    const mainMenu = document.getElementById('mainMenu');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            mainMenu.classList.toggle('active');
            
            // Cambiar ícono del botón
            const icon = menuToggle.querySelector('i');
            if (mainMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
</script>

</body>
</html>