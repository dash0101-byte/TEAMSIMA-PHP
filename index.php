<?php
session_start();

$rol = $_SESSION['rol'] ?? '';
$puede_ver_sistema = ($rol === 'admin' || $rol === 'cliente');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIMA - Sistema Integrado de Monitoreo Ambiental</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../public/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/css/styles.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .hero-section {
            min-height: 78vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 70px 20px;
        }

        .hero-content {
            max-width: 1000px;
            margin: auto;
        }

        .hero-content h2 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            max-width: 720px;
            margin: 0 auto 25px;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 35px;
        }

        .secondary-button {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .secondary-button:hover {
            background: white;
            color: #111;
        }

        .hero-image img {
            width: 100%;
            max-width: 720px;
            height: 360px;
            object-fit: cover;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.35);
        }

        .quick-info {
            padding: 60px 20px;
            background: #f8f9fa;
        }

        .quick-grid {
            max-width: 1100px;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
        }

        .quick-box {
            background: white;
            padding: 28px;
            text-align: center;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .quick-box:hover {
            transform: translateY(-6px);
        }

        .quick-box i {
            font-size: 2.6rem;
            color: var(--accent);
            margin-bottom: 15px;
        }

        .quick-box h3 {
            color: var(--dark);
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .quick-box p {
            color: #666;
            font-size: 0.95rem;
        }

        .locked-card {
            opacity: 0.65;
            cursor: not-allowed !important;
        }

        .locked-card::after {
            content: "Disponible para clientes y administradores";
            display: block;
            margin-top: 15px;
            background: var(--dark);
            color: white;
            padding: 8px;
            border-radius: 8px;
            font-size: 0.8rem;
        }

        .floating-info-btn {
            position: fixed;
            bottom: 95px;
            right: 25px;
            width: 65px;
            height: 65px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            z-index: 9999;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            transition: 0.3s;
        }

        .floating-info-btn:hover {
            transform: scale(1.1);
        }

        .info-panel {
            position: fixed;
            bottom: 170px;
            right: 25px;
            width: 480px;
            max-width: calc(100vw - 50px);
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
            z-index: 9998;
            overflow: hidden;
            display: none;
        }

        .info-panel.show {
            display: block;
        }

        .info-panel-header {
            background: var(--dark);
            color: white;
            padding: 18px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-panel-header h3 {
            font-size: 1.1rem;
            margin: 0;
        }

        .close-panel {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.7rem;
            cursor: pointer;
        }

        .info-panel-body {
            padding: 22px;
            max-height: 500px;
            overflow-y: auto;
        }

        .info-panel-body h4 {
            color: var(--dark);
            margin-top: 18px;
            border-left: 4px solid var(--accent);
            padding-left: 10px;
        }

        .info-panel-body p,
        .info-panel-body li {
            color: #555;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .footer-sima {
            background: var(--dark);
            color: white;
            padding: 40px 20px 20px;
        }

        .footer-content {
            max-width: 1100px;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        .footer-content h4 {
            color: white;
            margin-bottom: 12px;
        }

        .footer-content p {
            color: #ccc;
            margin: 5px 0;
            font-size: 0.9rem;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.15);
            margin-top: 30px;
            padding-top: 18px;
            font-size: 0.85rem;
            color: #aaa;
        }

        @media (max-width: 768px) {
            .hero-content h2 {
                font-size: 2.7rem;
            }

            .hero-image img {
                height: 240px;
            }

            .info-panel {
                right: 15px;
                bottom: 150px;
            }
        }
    </style>
</head>

<body>

<?php include 'menu.php'; ?>

<section class="hero-section">
    <div class="hero-content">

        <h2>SIMA</h2>

        <p class="hero-subtitle">
            Plataforma inteligente de monitoreo ambiental mediante drones, tecnología GPS y gestión digital de vuelos.
        </p>

        <div class="hero-buttons">
            <a href="#features" class="cta-button">
                Explorar sistema
            </a>

            <button class="secondary-button" onclick="toggleInfoPanel()">
                Más información
            </button>
        </div>

        <div class="hero-image">
            <img src="img/dron_basico.png" alt="Dron SIMA">
        </div>

    </div>
</section>

<section class="quick-info">
    <div class="quick-grid">

        <div class="quick-box">
            <i class="fas fa-drone"></i>
            <h3>Drones inteligentes</h3>
            <p>Supervisión ambiental mediante dispositivos aéreos y sensores aplicados.</p>
        </div>

        <div class="quick-box">
            <i class="fas fa-map-location-dot"></i>
            <h3>GPS en tiempo real</h3>
            <p>Ubicación y seguimiento del dron dentro de zonas específicas de monitoreo.</p>
        </div>

        <div class="quick-box">
            <i class="fas fa-leaf"></i>
            <h3>Impacto ambiental</h3>
            <p>Apoyo al análisis de zonas urbanas, áreas verdes y condiciones ambientales.</p>
        </div>

    </div>
</section>

<section class="features" id="features">

    <h2>Funciones de la Plataforma</h2>

    <div class="grid">

        <div class="card <?php echo !$puede_ver_sistema ? 'locked-card' : ''; ?>"
            <?php if ($puede_ver_sistema): ?>
                onclick="location.href='mapa_gps.php'"
            <?php endif; ?>
            data-bs-toggle="tooltip"
            title="Visualización GPS del dron">

            <i class="fas fa-map-marked-alt"></i>
            <h3>Monitoreo Ambiental</h3>
            <p>
                Visualización de ubicación, seguimiento del dron y consulta de datos relevantes del entorno.
            </p>
            <small style="color: var(--secondary);">Seguimiento organizado</small>
        </div>

        <div class="card"
             onclick="location.href='comentarios.php'"
             data-bs-toggle="tooltip"
             title="Comentarios y observaciones">

            <i class="fas fa-comments"></i>
            <h3>Comentarios y Reportes</h3>
            <p>
                Espacio para registrar observaciones, reportes o sugerencias relacionadas con el sistema.
            </p>
            <small style="color: var(--secondary);">Comunicación y mejora continua</small>
        </div>

        <div class="card <?php echo !$puede_ver_sistema ? 'locked-card' : ''; ?>"
            <?php if ($puede_ver_sistema): ?>
                onclick="location.href='dash2.php'"
            <?php endif; ?>
            data-bs-toggle="tooltip"
            title="Panel de control">

            <i class="fas fa-chart-bar"></i>
            <h3>Panel de Control</h3>
            <p>
                Consulta de información operativa, registros, métricas y herramientas internas de seguimiento.
            </p>
            <small style="color: var(--secondary);">Datos para decisiones</small>
        </div>

    </div>
</section>

<section class="contenedor" id="modelos">

    <h2 style="text-align: center; margin-bottom: 40px; color: var(--dark);">
        Soluciones de Monitoreo Ambiental SIMA
    </h2>

    <div class="grid">

        <div class="card">
            <img src="img/dron_basico.png"
                 alt="Dron para monitoreo urbano"
                 style="width:100%; border-radius:10px; height: 200px; object-fit: cover; margin-bottom:15px;">

            <h3>Monitoreo Urbano</h3>

            <p style="color: var(--secondary); font-weight: bold; margin: 10px 0;">
                Supervisión ligera
            </p>

            <p style="font-size: 0.9rem; color: #666;">
                Recorridos rápidos en zonas urbanas, campus, parques y espacios de vigilancia ambiental básica.
            </p>

            <ul style="list-style: none; text-align: left; padding: 15px 0; color: #555;">
                <li><i class="fas fa-battery-half"></i> Autonomía estimada: 20 minutos</li>
                <li><i class="fas fa-wifi"></i> Alcance estimado: 1 km</li>
                <li><i class="fas fa-camera"></i> Cámara HD</li>
                <li><i class="fas fa-city"></i> Zonas urbanas controladas</li>
            </ul>
        </div>

        <div class="card" style="border-top: 4px solid var(--accent);">
            <img src="img/dron_pro.png"
                 alt="Dron para análisis ambiental"
                 style="width:100%; border-radius:10px; height: 200px; object-fit: cover; margin-bottom:15px;">

            <h3>Análisis Ambiental</h3>

            <p style="color: var(--secondary); font-weight: bold; margin: 10px 0;">
                Supervisión avanzada
            </p>

            <p style="font-size: 0.9rem; color: #666;">
                Monitoreo de zonas verdes, cultivos, áreas naturales y condiciones ambientales específicas.
            </p>

            <ul style="list-style: none; text-align: left; padding: 15px 0; color: #555;">
                <li><i class="fas fa-battery-full"></i> Autonomía estimada: 45 minutos</li>
                <li><i class="fas fa-wifi"></i> Alcance estimado: 5 km</li>
                <li><i class="fas fa-camera"></i> Cámara 4K</li>
                <li><i class="fas fa-leaf"></i> Sensores ambientales</li>
            </ul>
        </div>

        <div class="card">
            <img src="img/dron_ind.png"
                 alt="Dron para supervisión industrial"
                 style="width:100%; border-radius:10px; height: 200px; object-fit: cover; margin-bottom:15px;">

            <h3>Supervisión Industrial</h3>

            <p style="color: var(--secondary); font-weight: bold; margin: 10px 0;">
                Operación de alta resistencia
            </p>

            <p style="font-size: 0.9rem; color: #666;">
                Enfocado en infraestructura, zonas de riesgo, áreas extensas y supervisión técnica especializada.
            </p>

            <ul style="list-style: none; text-align: left; padding: 15px 0; color: #555;">
                <li><i class="fas fa-battery-full"></i> Autonomía estimada: 60 minutos</li>
                <li><i class="fas fa-wifi"></i> Alcance estimado: 10 km</li>
                <li><i class="fas fa-microchip"></i> Sensores intercambiables</li>
                <li><i class="fas fa-shield-alt"></i> Resistencia a condiciones difíciles</li>
            </ul>
        </div>

    </div>

</section>

<button class="floating-info-btn" onclick="toggleInfoPanel()">
    <i class="fas fa-circle-info"></i>
</button>

<div class="info-panel" id="infoPanel">
    <div class="info-panel-header">
        <h3><i class="fas fa-circle-info"></i> Información del proyecto</h3>
        <button class="close-panel" onclick="closeInfoPanel()">&times;</button>
    </div>

    <div class="info-panel-body">

        <h4>Alcance del proyecto</h4>
        <p>
            SIMA permite registrar vuelos, visualizar información operativa y apoyar el monitoreo ambiental mediante un prototipo basado en dron y plataforma web.
        </p>

        <h4>Limitantes</h4>
        <ul>
            <li>El prototipo se encuentra en etapa de desarrollo académico.</li>
            <li>No sustituye una estación meteorológica profesional.</li>
            <li>Algunas funciones dependen de la conexión local y del prototipo físico.</li>
        </ul>

        <h4>Tecnología aplicada</h4>
        <ul>
            <li>PHP para la lógica del servidor.</li>
            <li>MySQL para almacenamiento de datos.</li>
            <li>Bootstrap, HTML, CSS y JavaScript para la interfaz.</li>
            <li>Dron con integración GPS y posibles sensores ambientales.</li>
        </ul>

        <h4>Niveles de usuario</h4>
        <ul>
            <li><strong>Administrador:</strong> gestiona usuarios, vuelos y paneles internos.</li>
            <li><strong>Cliente:</strong> consulta vuelos, mapa GPS y datos del sistema.</li>
            <li><strong>Vista previa:</strong> visualiza información general sin acceder a datos internos.</li>
        </ul>

        <h4>Impacto ambiental</h4>
        <p>
            El sistema se orienta a apoyar la supervisión ambiental y la toma de decisiones mediante información visual, ubicación GPS y registro digital.
        </p>

    </div>
</div>

<footer class="footer-sima">
    <div class="footer-content">

        <div>
            <h4>SIMA</h4>
            <p>Sistema Integrado de Monitoreo Ambiental.</p>
            <p>Proyecto académico con enfoque tecnológico y ambiental.</p>
        </div>

        <div>
            <h4>Tecnologías</h4>
            <p>PHP</p>
            <p>MySQL</p>
            <p>Bootstrap</p>
            <p>JavaScript</p>
        </div>

        <div>
            <h4>Funciones</h4>
            <p>Monitoreo GPS</p>
            <p>Gestión de vuelos</p>
            <p>Dashboard</p>
            <p>Gestión de usuarios</p>
        </div>

    </div>

    <div class="footer-bottom">
        © 2025 SIMA | Proyecto desarrollado por Jocabed Montoya
    </div>
</footer>

<div class="scroll-to-top" onclick="scrollToTop()">
    <i class="fas fa-arrow-up"></i>
</div>

<script src="public/js/bootstrap.bundle.min.js"></script>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(t => new bootstrap.Tooltip(t));

    window.addEventListener('scroll', function() {
        const scrollBtn = document.querySelector('.scroll-to-top');
        if (scrollBtn) {
            if (window.scrollY > 300) {
                scrollBtn.classList.add('show');
            } else {
                scrollBtn.classList.remove('show');
            }
        }
    });

    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function toggleInfoPanel() {
        document.getElementById('infoPanel').classList.toggle('show');
    }

    function closeInfoPanel() {
        document.getElementById('infoPanel').classList.remove('show');
    }

    document.addEventListener('click', function(event) {
        const panel = document.getElementById('infoPanel');
        const btn = document.querySelector('.floating-info-btn');
        const secondaryBtn = document.querySelector('.secondary-button');

        if (panel.classList.contains('show')) {
            if (!panel.contains(event.target) && !btn.contains(event.target) && !secondaryBtn.contains(event.target)) {
                closeInfoPanel();
            }
        }
    });
</script>

</body>
</html>