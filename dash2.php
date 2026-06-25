<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Monitoreo Ambiental</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

    <!-- Fuentes e iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ===================== ESTILO BASE DARK THEME ===================== */
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --secondary: #1e293b;
            --secondary-dark: #0f172a;
            --accent: #10b981;
            --accent-dark: #059669;
            --light: #f1f5f9;
            --dark: #0f172a;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --warning: #f59e0b;
            --warning-dark: #d97706;
            
            /* Dark theme specific */
            --bg-primary: #0a0e17;
            --bg-secondary: #111827;
            --bg-tertiary: #1e293b;
            --bg-card: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: #1e293b;
            --border-light: #334155;
            
            --shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.5);
            --shadow-glow: 0 0 20px rgba(59, 130, 246, 0.3);
            --transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-bounce: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            --radius: 18px;
            --radius-sm: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0e17 0%, #0f172a 50%, #1a1f2e 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Fondo con grid sutil */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }

        /* Scrollbar dark */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--accent-dark));
        }

        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }

        .dashboard {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            animation: fadeInUp 0.8s ease-out;
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

        .page-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title h2 {
            font-size: 2.6rem;
            position: relative;
            display: inline-block;
            margin-bottom: 13px;
            background: linear-gradient(135deg, #fff, var(--accent));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .page-title h2::after {
            content: '';
            position: absolute;
            bottom: -7px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--primary));
            border-radius: 4px;
        }

        .page-title p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .card {
            background: var(--bg-card);
            padding: 28px 26px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            border-top: 4px solid var(--primary);
            transition: var(--transition-bounce);
            height: 320px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            transition: left 0.5s;
            pointer-events: none;
        }

        .card:hover::before {
            left: 100%;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(59, 130, 246, 0.5);
        }

        .card-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 1.5rem;
        }

        .card-header i {
            font-size: 2.6rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: var(--transition);
        }

        .card:hover .card-header i {
            transform: scale(1.1);
            filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.5));
        }

        .card-value {
            margin: 18px 0 6px;
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            color: var(--text-primary);
        }

        .card-value .unit {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-left: 4px;
        }

        .card-sub {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .card-status {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: .85rem;
            font-weight: 600;
        }

        .mini-chart {
            max-height: 70px;
            margin-top: 16px;
        }

        .card-status {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
            padding: 10px 26px;
            border-radius: 24px;
            font-size: 1.05rem;
            font-weight: 700;
            text-align: center;
            letter-spacing: .3px;
        }

        /* Colores semáforo en dark */
        .status-ok {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-warn {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-bad {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-global {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 22px;
            border-radius: 25px;
            font-weight: 600;
        }

        .bg-ok {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .bg-warn {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .bg-bad {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

       .alert-banner {
    margin-bottom: 25px;
    padding: 15px 20px;
    border-radius: var(--radius-sm);
    font-weight: 600;
    background: rgba(239, 68, 68, 0.1);
    color: #ffffff;  /* ← Ahora es blanco */
    border-left: 4px solid var(--danger);
    animation: slideInLeft 0.4s ease-out;
}

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .control-panel {
            background: var(--bg-card);
            padding: 30px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-top: 40px;
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .control-panel:hover {
            box-shadow: var(--shadow-lg);
            border-color: rgba(59, 130, 246, 0.3);
        }

        footer {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            color: var(--text-secondary);
            text-align: center;
            padding: 25px;
            margin-top: 40px;
            position: relative;
            border-top: 1px solid var(--border);
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--primary), var(--accent));
        }

        footer p {
            transition: opacity 0.3s;
        }

        footer p:hover {
            opacity: 1;
            color: var(--text-primary);
        }

        .card-drone {
            border-top-color: var(--accent);
        }

        .card-drone .card-header i {
            background: linear-gradient(135deg, var(--accent), var(--primary));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Botón flotante volver arriba */
        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: var(--shadow);
            border: none;
        }

        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        /* Selección de texto */
        ::selection {
            background: var(--accent);
            color: white;
        }

        ::-moz-selection {
            background: var(--accent);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                margin: 20px auto;
            }
            
            .page-title h2 {
                font-size: 2rem;
            }
            
            .grid {
                gap: 20px;
            }
            
            .card {
                padding: 20px;
                height: auto;
                min-height: 280px;
            }
            
            .card-value {
                font-size: 2rem;
            }
            
            .scroll-to-top {
                bottom: 20px;
                right: 20px;
                width: 45px;
                height: 45px;
            }
        }

        @media (max-width: 480px) {
            .page-title h2 {
                font-size: 1.8rem;
            }
            
            .page-title p {
                font-size: 0.95rem;
            }
            
            .card-header {
                font-size: 1.2rem;
            }
            
            .card-header i {
                font-size: 2rem;
            }
            
            .card-value {
                font-size: 1.8rem;
            }
            
            .card-sub {
                font-size: 1rem;
            }
            
            .card-status {
                padding: 6px 16px;
                font-size: 0.85rem;
            }
            
            .control-panel {
                padding: 20px;
            }
        }

        /* Accesibilidad */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Efecto de carga para elementos */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .loading {
            animation: pulse 1.5s ease-in-out infinite;

        }
        .alert {
    background: rgba(59, 130, 246, 0.15);
    color: #ffffff;
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 12px;
}

.alert strong {
    color: #ffffff;
}

.alert-warning {
    background: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.3);
}

.alert-success {
    background: rgba(16, 185, 129, 0.15);
    border-color: rgba(16, 185, 129, 0.3);
}

.alert-info {
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.3);
}
    </style>

    <!-- Script para el botón de scroll to top -->
    <script>
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
    </script>
</head>

<body>
    <div class="main-container">
        <?php include "menu.php"; ?>

        <main>
            <section class="dashboard">

                <div class="page-title">
                    <h2><i class="fas fa-leaf"></i> Monitoreo Ambiental</h2>
                    <p>Calidad del aire y condiciones ambientales en tiempo real</p>
                    <div id="globalBadge" class="badge-global bg-ok">Ambiente saludable</div>
                </div>

                <div id="alerta" class="alert-banner bg-bad" style="display:none"></div>

                <div class="grid">

                    <div class="card card-temperature" data-bs-toggle="tooltip" title="Ideal: 19–24 °C">

                        <div class="card-header">
                            <i class="fas fa-thermometer-half"></i>
                            <span>Temperatura</span>
                        </div>

                        <div class="card-value">
                            <span id="temperature">--</span><span class="unit">°C</span>
                        </div>

                        <div class="card-sub" id="trendTemp">Estable</div>

                        <div class="card-status" id="tempStatus">--</div>

                        <canvas id="chartTemp" class="mini-chart"></canvas>
                    </div>


                    <div class="card card-humidity" data-bs-toggle="tooltip" title="Ideal: 40–60 %">

                        <div class="card-header">
                            <i class="fas fa-tint"></i>
                            <span>Humedad</span>
                        </div>

                        <div class="card-value">
                            <span id="humidity">--</span><span class="unit">%</span>
                        </div>

                        <div class="card-sub" id="trendHum">Estable</div>

                        <div class="card-status" id="humStatus">--</div>

                        <canvas id="chartHum" class="mini-chart"></canvas>
                    </div>


                    <div class="card card-co2" data-bs-toggle="tooltip" title="Ideal: < 800 ppm">

                        <div class="card-header">
                            <i class="fas fa-wind"></i>
                            <span>CO₂</span>
                        </div>

                        <div class="card-value">
                            <span id="co2">--</span><span class="unit">ppm</span>
                        </div>

                        <div class="card-sub" id="trendCO2">Estable</div>

                        <div class="card-status" id="co2Status">--</div>

                        <canvas id="chartCO2" class="mini-chart"></canvas>
                    </div>


                    <div class="card card-pressure">

                        <div class="card-header">
                            <i class="fas fa-gauge-high"></i>
                            <span>Presión</span>
                        </div>

                        <div class="card-value">
                            <span id="pressure">--</span><span class="unit">hPa</span>
                        </div>

                        <div class="card-sub">Atmosférica</div>

                        <div class="card-status status-ok">OK</div>

                        <canvas id="chartPres" class="mini-chart"></canvas>
                    </div>

                    <div class="card card-drone">

                        <div class="card-header">
                            <i class="fas fa-drone"></i>
                            <span>Estado del dron</span>
                        </div>

                        <div class="card-value" style="font-size:1.6rem">
                            <span id="droneState">—</span>
                        </div>

                        <div class="card-sub" id="droneLocation">
                            Ubicación desconocida
                        </div>

                        <div class="card-status status-warn" id="droneStatus">
                            Sin señal
                        </div>

                        <div class="card-sub" style="font-size:.9rem;margin-top:10px">
                            <span id="droneCoords">Lat: — | Lon: —</span>
                        </div>

                    </div>


                    <div class="card card-gas">

                        <div class="card-header">
                            <i class="fas fa-flask"></i>
                            <span>VOC / Gas</span>
                        </div>

                        <div class="card-value">
                            <span id="gas">--</span><span class="unit">Ω</span>
                        </div>

                        <div class="card-sub" id="trendGas">Estable</div>

                        <div class="card-status" id="gasStatus">--</div>

                        <canvas id="chartGas" class="mini-chart"></canvas>
                    </div>


                </div>

                <div class="control-panel">
    <h3 class="control-title"><i class="fas fa-info-circle"></i> Estado de la Sesión</h3>
    
    <?php $rol_actual = $_SESSION['rol'] ?? 'vista_previa'; ?>

    <?php if($rol_actual === 'vista_previa'): ?>
        <div class="alert alert-warning text-center">
            <i class="fas fa-eye"></i> <strong>Modo Vista Previa</strong><br>
            Estás visualizando los datos del dron en tiempo real.
        </div>

    <?php elseif($rol_actual === 'cliente'): ?>
        <div class="alert alert-success text-center">
            <i class="fas fa-user-check"></i> <strong>Cliente Verificado</strong><br>
            Tienes acceso a los datos premium del dron.
        </div>

    <?php elseif($rol_actual === 'admin'): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-user-shield"></i> <strong>Administrador</strong><br>
            Acceso total al sistema de monitoreo.
        </div>
    <?php endif; ?>
</div>

            </section>
        </main>

        <footer>
            <p>© 2025 Dron Magnético</p>
        </footer>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        const ENV_URL = "http://172.16.117.50/env";

        const hist = { t: [], h: [], c: [], g: [] };

        function push(arr, val) {
            arr.push(val);
            if (arr.length > 15) arr.shift();
        }

        function trend(arr) {
            if (arr.length < 2) return { icon: "→", cls: "trend-flat", txt: "Estable" };
            const d = arr[arr.length - 1] - arr[arr.length - 2];
            if (d > 0.5) return { icon: "↑", cls: "trend-up", txt: "Subiendo" };
            if (d < -0.5) return { icon: "↓", cls: "trend-down", txt: "Bajando" };
            return { icon: "→", cls: "trend-flat", txt: "Estable" };
        }

        function setStatus(el, txt, lvl) {
            el.textContent = txt;
            el.className = "card-status " +
                (lvl === "ok" ? "status-ok" :
                    lvl === "warn" ? "status-warn" : "status-bad");
        }

        function calcIAQ(d) {
            let score = 100;
            if (d.co2 > 1200) score -= 40;
            else if (d.co2 > 800) score -= 20;

            if (d.humidity < 30 || d.humidity > 70) score -= 15;
            if (d.gas < 80000) score -= 10;

            return Math.max(0, score);
        }

        function update() {
            fetch(ENV_URL)
                .then(r => r.json())
                .then(d => {

                    // HISTORIAL
                    push(hist.t, d.temperature);
                    push(hist.h, d.humidity);
                    push(hist.c, d.co2);
                    push(hist.g, d.gas);

                    // VALORES
                    temperature.textContent = d.temperature.toFixed(1);
                    humidity.textContent = d.humidity.toFixed(1);
                    co2.textContent = Math.round(d.co2);
                    pressure.textContent = d.pressure.toFixed(1);
                    gas.textContent = Math.round(d.gas);

                    // TENDENCIAS
                    const tt = trend(hist.t);
                    const th = trend(hist.h);
                    const tc = trend(hist.c);
                    const tg = trend(hist.g);

                    trendTemp.textContent = `${tt.icon} ${tt.txt}`;
                    trendTemp.className = tt.cls;

                    trendHum.textContent = `${th.icon} ${th.txt}`;
                    trendHum.className = th.cls;

                    trendCO2.textContent = `${tc.icon} ${tc.txt}`;
                    trendCO2.className = tc.cls;

                    trendGas.textContent = `${tg.icon} ${tg.txt}`;
                    trendGas.className = tg.cls;

                    // ESTADOS POR SENSOR
                    setStatus(
                        tempStatus,
                        d.temperature < 18 ? "Frío" :
                            d.temperature > 28 ? "Caliente" : "Confort",
                        d.temperature < 18 || d.temperature > 28 ? "warn" : "ok"
                    );

                    setStatus(
                        humStatus,
                        d.humidity < 30 ? "Seco" :
                            d.humidity > 70 ? "Húmedo" : "Ideal",
                        d.humidity < 30 || d.humidity > 70 ? "warn" : "ok"
                    );

                    setStatus(
                        co2Status,
                        d.co2 < 600 ? "Excelente" :
                            d.co2 < 1000 ? "Aceptable" : "Alto",
                        d.co2 < 600 ? "ok" : d.co2 < 1000 ? "warn" : "bad"
                    );

                    setStatus(
                        gasStatus,
                        d.gas > 100000 ? "Bueno" : "Cuidado",
                        d.gas > 100000 ? "ok" : "warn"
                    );

                    // IAQ
                    const iaq = calcIAQ(d);
                    document.getElementById("iaq").textContent = iaq;

                    globalBadge.className =
                        "badge-global " +
                        (iaq > 80 ? "bg-ok" :
                            iaq > 60 ? "bg-warn" : "bg-bad");

                    globalBadge.textContent =
                        iaq > 80 ? "Ambiente saludable" :
                            iaq > 60 ? "Ventilación recomendada" :
                                "Mala calidad del aire";

                    // ALERTA
                    if (d.co2 > 1200) {
                        alerta.style.display = "block";
                        alerta.textContent = "⚠ CO₂ alto: ventila inmediatamente";
                    } else {
                        alerta.style.display = "none";
                    }

                    // RECOMENDACIONES
                    recs.innerHTML = "";
                    if (d.co2 > 1000) recs.innerHTML += "<li>Ventila el espacio 5–10 minutos.</li>";
                    if (d.humidity > 70) recs.innerHTML += "<li>Humedad elevada, riesgo de moho.</li>";
                    if (d.humidity < 30) recs.innerHTML += "<li>Aire seco, posible irritación.</li>";
                });
        }

        setInterval(update, 3000);
        update();

        async function getLocationName(lat, lon) {
            try {
                const url =
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`;
                const res = await fetch(url, {
                    headers: { "Accept-Language": "es" }
                });
                const d = await res.json();
                const a = d.address || {};

                return [
                    a.suburb || a.neighbourhood || a.village || "",
                    a.city || a.municipality || "",
                    a.state || ""
                ].filter(Boolean).join(", ");
            } catch {
                return "Ubicación no disponible";
            }
        }


        const GPS_URL = "http://172.16.117.50/gps";
        let lastGeoTime = 0;

        async function updateDroneStatus() {
            try {
                const res = await fetch(GPS_URL, { cache: "no-store" });
                const data = await res.json();

                if (!data.lat || !data.lon) throw "No fix";

                // ESTADO
                droneState.textContent = "En operación";
                droneStatus.textContent = "Activo";
                droneStatus.className = "card-status status-ok";

                droneCoords.textContent =
                    `Lat: ${data.lat.toFixed(5)} | Lon: ${data.lon.toFixed(5)}`;

                // Reverse geocoding (cada 15 s)
                if (Date.now() - lastGeoTime > 15000) {
                    lastGeoTime = Date.now();
                    const loc = await getLocationName(data.lat, data.lon);
                    droneLocation.textContent = loc;
                }

            } catch {
                droneState.textContent = "Desconectado";
                droneStatus.textContent = "Sin señal";
                droneStatus.className = "card-status status-bad";
                droneLocation.textContent = "—";
                droneCoords.textContent = "Lat: — | Lon: —";
            }
        }
        setInterval(updateDroneStatus, 5000);
        updateDroneStatus();


    </script>

</body>

</html>