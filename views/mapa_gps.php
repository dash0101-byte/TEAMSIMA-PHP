<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mapa GPS - Dron Magnético</title>

    <link rel="stylesheet" href="../public/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    

   <style>
    :root {
        --primary: #00d4ff;
        --primary-dark: #0099cc;
        --primary-glow: rgba(0, 212, 255, 0.5);
        
        --accent: #22c55e;
        --accent-dark: #16a34a;
        --accent-glow: rgba(34, 197, 94, 0.4);
        
        --secondary: #1e293b;
        --light: #f8fafc;
        --dark: #0f172a;
        --darker: #020617;
        
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        --shadow-hover: 0 15px 40px rgba(0, 212, 255, 0.15);
        --shadow-glow: 0 0 20px rgba(0, 212, 255, 0.2);
        
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --radius: 20px;
        --radius-sm: 12px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', 'Inter', system-ui, sans-serif;
        background: linear-gradient(135deg, #0a0e17 0%, #0f172a 50%, #1a1f2e 100%);
        background-attachment: fixed;
        min-height: 100vh;
        position: relative;
    }

    /* Efecto de partículas de fondo oscuro */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: 
            radial-gradient(circle at 20% 40%, rgba(0, 212, 255, 0.06) 0%, transparent 40%),
            radial-gradient(circle at 80% 60%, rgba(34, 197, 94, 0.04) 0%, transparent 40%),
            repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0px, rgba(255,255,255,0.02) 2px, transparent 2px, transparent 8px);
        pointer-events: none;
        z-index: 0;
    }

    .contenedor {
        flex: 1;
        max-width: 1400px;
        margin: 50px auto;
        padding: 0 24px;
        position: relative;
        z-index: 1;
        animation: fadeInUp 0.6s ease-out;
    }

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

    /* Título principal */
    .page-title {
        text-align: center;
        margin-bottom: 50px;
    }

    .page-title h2 {
        font-size: 3rem;
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
        background: linear-gradient(135deg, #fff, #94a3b8, #cbd5e1);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .page-title h2::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(90deg, transparent, var(--accent), var(--primary), transparent);
        border-radius: 4px;
        animation: glowLine 2s ease-in-out infinite;
    }

    @keyframes glowLine {
        0%, 100% { opacity: 0.6; width: 80px; }
        50% { opacity: 1; width: 120px; }
    }

    .page-title p {
        font-size: 1.15rem;
        color: #94a3b8;
        max-width: 700px;
        margin: 20px auto 0;
        font-weight: 500;
    }

    /* Contenedor del mapa con glassmorphism oscuro */
    .map-container {
        background: rgba(30, 41, 59, 0.95);
        backdrop-filter: blur(10px);
        padding: 25px;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 35px;
        transition: var(--transition);
        border: 1px solid rgba(51, 65, 85, 0.5);
        position: relative;
        overflow: hidden;
    }

    .map-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
        animation: gradientShift 3s ease infinite;
        background-size: 200% 100%;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .map-container:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-hover);
    }

    #map {
        height: 700px;
        border-radius: var(--radius-sm);
        border: 2px solid rgba(0, 212, 255, 0.2);
        transition: var(--transition);
        box-shadow: inset 0 0 20px rgba(0,0,0,0.2);
    }

    #map:hover {
        border-color: rgba(0, 212, 255, 0.5);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.1);
    }

    /* Panel de información oscuro */
    .info-panel {
        background: rgba(30, 41, 59, 0.95);
        backdrop-filter: blur(10px);
        padding: 30px;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        transition: var(--transition);
        border: 1px solid rgba(51, 65, 85, 0.5);
    }

    .info-panel:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-hover);
    }

    /* Grid de tarjetas */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    /* Tarjetas oscuras */
    .info-card {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        padding: 35px 20px;
        border-radius: var(--radius-sm);
        text-align: center;
        border-top: 4px solid var(--primary);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0,212,255,0.08), transparent);
        transition: left 0.5s;
    }

    .info-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        border-top-color: var(--accent);
    }

    .info-card:hover::before {
        left: 100%;
    }

    .info-card i {
        font-size: 3rem;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        margin-bottom: 18px;
        transition: var(--transition);
        display: inline-block;
    }

    .info-card:hover i {
        transform: scale(1.1) rotate(5deg);
    }

    .info-card h4 {
        color: #f1f5f9;
        margin-bottom: 12px;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .info-card p {
        color: #94a3b8;
        margin: 0;
        font-size: 1rem;
        font-weight: 500;
    }

    /* Sección de estado oscura */
    .status-section {
        text-align: center;
        padding: 25px;
        background: rgba(30, 41, 59, 0.9);
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0,212,255,0.2);
        backdrop-filter: blur(5px);
        transition: var(--transition);
    }

    .status-section:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-glow);
    }

    .status-item {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 14px 28px;
        background: #1e293b;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transition: var(--transition);
    }

    .status-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,212,255,0.15);
    }

    .status-item span {
        color: #f1f5f9;
    }

    .status-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), #27ae60);
        animation: blink 1.5s ease-in-out infinite;
        box-shadow: 0 0 8px rgba(34,197,94,0.5);
    }

    @keyframes blink {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    /* Pulse de GPS */
    .gps-pulse {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        box-shadow: 0 0 0 0 rgba(0, 212, 255, 0.8);
        animation: pulse 2s infinite;
        cursor: pointer;
        transition: var(--transition);
    }

    .gps-pulse:hover {
        transform: scale(1.05);
    }

    @keyframes pulse {
        0% {
            transform: scale(0.8);
            opacity: 0.9;
            box-shadow: 0 0 0 0 rgba(0, 212, 255, 0.7);
        }
        70% {
            transform: scale(1.2);
            opacity: 0.5;
            box-shadow: 0 0 0 20px rgba(0, 212, 255, 0);
        }
        100% {
            transform: scale(0.8);
            opacity: 0.9;
            box-shadow: 0 0 0 0 rgba(0, 212, 255, 0);
        }
    }

    /* Footer oscuro */
    footer {
        background: linear-gradient(135deg, var(--dark), var(--darker));
        color: #94a3b8;
        text-align: center;
        padding: 30px 20px;
        margin-top: 60px;
        position: relative;
        z-index: 1;
    }

    footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--primary), var(--accent), transparent);
    }

    footer p {
        margin: 0;
        opacity: 0.9;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    /* Estilos para Leaflet (mapa) */
    .leaflet-div-icon {
        background: transparent !important;
        border: none !important;
    }

    .leaflet-control-center {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        border-radius: 50%;
        box-shadow: 0 4px 15px rgba(0,212,255,0.4);
        cursor: pointer;
        padding: 10px;
        transition: var(--transition);
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .leaflet-control-center:hover {
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 6px 20px rgba(34,197,94,0.5);
    }

    .leaflet-control-center i {
        font-size: 20px;
        color: white;
    }

    /* Personalización de controles de Leaflet oscuros */
    .leaflet-control-zoom a {
        background: #1e293b;
        color: var(--primary);
        transition: var(--transition);
    }

    .leaflet-control-zoom a:hover {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: white;
    }

    /* Animación de carga para el mapa */
    @keyframes mapFadeIn {
        from {
            opacity: 0;
            transform: scale(0.98);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    #map {
        animation: mapFadeIn 0.8s ease-out;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .contenedor {
            margin: 40px auto;
            padding: 0 20px;
        }

        #map {
            height: 550px;
        }
    }

    @media (max-width: 768px) {
        .contenedor {
            margin: 30px auto;
            padding: 0 16px;
        }

        .page-title h2 {
            font-size: 2.2rem;
        }

        .page-title p {
            font-size: 1rem;
        }

        .map-container {
            padding: 18px;
        }

        #map {
            height: 450px;
        }

        .info-panel {
            padding: 20px;
        }

        .info-grid {
            gap: 15px;
        }

        .info-card {
            padding: 25px 15px;
        }

        .info-card i {
            font-size: 2.5rem;
        }

        .info-card h4 {
            font-size: 1.1rem;
        }

        .status-item {
            padding: 10px 20px;
        }
    }

    @media (max-width: 480px) {
        .page-title h2 {
            font-size: 1.8rem;
        }

        #map {
            height: 350px;
        }

        .info-card {
            padding: 20px 12px;
        }

        .info-card i {
            font-size: 2rem;
        }

        .gps-pulse {
            width: 40px;
            height: 40px;
        }

        .leaflet-control-center {
            width: 36px;
            height: 36px;
        }
    }

    /* Scrollbar personalizada oscura */
    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    ::-webkit-scrollbar-track {
        background: #1e293b;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--accent), var(--primary));
    }

    /* Selección de texto */
    ::selection {
        background: var(--accent);
        color: white;
    }
</style>
</head>

<body>
    <div class="main-container">

        <?php include "menu.php"; ?>

        <section class="contenedor">
            <div class="page-title">
                <h2><i class="fas fa-map-marked-alt"></i> Mapa GPS del Dron</h2>
                <p>Seguimiento en tiempo real de la ubicación del dron magnético</p>
            </div>

            <div class="map-container">
                <div id="map"></div>
            </div>

            <div class="info-panel">
                <div class="info-grid">
                    <div class="info-card">
                        <i class="fas fa-satellite-dish"></i>
                        <h4>Satélites</h4>
                        <p id="gps-sats">—</p>
                    </div>

                    <div class="info-card">
                        <i class="fas fa-mountain"></i>
                        <h4>Altitud</h4>
                        <p id="gps-alt">—</p>
                    </div>

                    <div class="info-card">
                        <i class="fas fa-gauge-high"></i>
                        <h4>Velocidad</h4>
                        <p id="gps-speed">—</p>
                    </div>

                    <div class="info-card">
                        <i class="fas fa-clock"></i>
                        <h4>Latencia</h4>
                        <p id="gps-latency">—</p>
                    </div>
                </div>


                <div class="status-section">
                    <h3 class="status-title">Estado del Sistema</h3>

                    <div class="status-item">
                        <div class="status-dot"></div>
                        <span id="status-text">Esperando GPS…</span>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            <p>© 2025 Dron Magnético - Sistema de Monitoreo GPS</p>
        </footer>
    </div>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const ESP32_URL = "http://172.16.117.50/gps";

            const map = L.map('map').setView([0, 0], 3);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            const centerControl = L.control({ position: 'topright' });

centerControl.onAdd = function () {
    const div = L.DomUtil.create('div', 'leaflet-control-center');
    div.innerHTML = '<i class="fas fa-crosshairs"></i>';
    div.title = "Centrar dron";

    div.onclick = () => {
    const pos = dronDot.getLatLng();
    if (!pos) return;
    map.panTo(pos);
};

    return div;
};

centerControl.addTo(map);


            /* ⚫ CÍRCULO CENTRAL NEGRO FIJO */
            const dronDot = L.circleMarker([0, 0], {
                radius: 9,
                color: '#ffffff',
                weight: 3,
                fillColor: '#161212ff',
                fillOpacity: 1
            }).addTo(map);

            let pulseMarker = null;
            let firstFix = true;

            function darkenColor(hex, factor = 0.35) {
                hex = hex.replace('#', '');

                let r = parseInt(hex.substring(0, 2), 16);
                let g = parseInt(hex.substring(2, 4), 16);
                let b = parseInt(hex.substring(4, 6), 16);

                r = Math.max(0, Math.floor(r * (1 - factor)));
                g = Math.max(0, Math.floor(g * (1 - factor)));
                b = Math.max(0, Math.floor(b * (1 - factor)));

                return '#' +
                    r.toString(16).padStart(2, '0') +
                    g.toString(16).padStart(2, '0') +
                    b.toString(16).padStart(2, '0');
            }

            let pathCoords = [];
const pathLine = L.polyline(pathCoords, {
    color: '#3498db',
    weight: 3,
    opacity: 0.8
}).addTo(map);

            setInterval(async () => {
                const t0 = performance.now();
                try {
                    const res = await fetch(ESP32_URL);
                    const data = await res.json();
                    const latency = Math.round(performance.now() - t0);

                    if (!data.lat || !data.lon) return;

                    const pos = [data.lat, data.lon];
                    dronDot.setLatLng(pos);
let pathCoords = [];
const pathLine = L.polyline(pathCoords, {
    color: '#646464ff',
    weight: 3,
    opacity: 0.8
}).addTo(map);

                    if (!pulseMarker) {
                        pulseMarker = L.marker(pos, {
                            icon: L.divIcon({
                                html: '<div class="gps-pulse"></div>',
                                iconSize: [40, 40],
                                iconAnchor: [20, 20]
                            }),
                            interactive: false
                        }).addTo(map);
                    } else {
                        pulseMarker.setLatLng(pos);
                    }

                    if (firstFix) {
                        map.setView(pos, 19);
                        firstFix = false;
                    }

                    /* 🎨 COLOR SOLO PARA EL PULSO */
                    let color = '#e74c3c';
                    if (data.sats >= 8) color = '#1abc9c';
                    else if (data.sats >= 5) color = '#f1c40f';

                    const pulseEl = pulseMarker.getElement().firstChild;

                    pulseEl.style.background = color;
                    pulseEl.style.borderColor = darkenColor(color, 0.35);

                    document.getElementById("gps-sats").textContent = data.sats;
                    document.getElementById("gps-alt").textContent = data.alt + " m";
                    document.getElementById("gps-speed").textContent =
                        data.speed.toFixed(0) + " cm/s";
                    document.getElementById("gps-latency").textContent = latency + " ms";
                    document.getElementById("status-text").textContent = "Dron conectado";

                } catch {
                    document.getElementById("status-text").textContent = "ESP32 desconectado";
                }
            }, 1000);

        });
    </script>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>