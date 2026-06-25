<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mapa GPS - Dron Magnético</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../public/css/bootstrap.min.css">

    <!-- Fuentes e iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2c3e50;
            --accent: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --danger: #e74c3c;
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
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .main-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .contenedor {
            flex: 1;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 40px;
            color: var(--dark);
        }
        
        .page-title h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }
        
        .page-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--accent);
            border-radius: 2px;
        }
        
        .page-title p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .map-container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }
        
        #map {
            width: 100%;
            height: 700px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        
        /* Asegurar que el mapa se muestre correctamente */
        .leaflet-container {
            background: #e0e0e0;
        }
        
        .info-panel {
            background-color: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .info-card {
            background-color: var(--light);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border-top: 4px solid var(--primary);
            transition: var(--transition);
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .info-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .info-card h4 {
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .info-card p {
            color: #666;
            margin: 0;
            font-size: 1rem;
        }
        
        .status-section {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, var(--light), #ffffff);
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        
        .status-title {
            color: var(--dark);
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .status-items {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: white;
            border-radius: 25px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .status-item.online {
            color: #27ae60;
        }
        
        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .online .status-dot {
            background: #27ae60;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            text-align: center;
            padding: 25px 20px;
            margin-top: 50px;
        }
        
        footer p {
            margin: 0;
            opacity: 0.8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .contenedor {
                margin: 20px auto;
                padding: 0 15px;
            }
            
            .page-title h2 {
                font-size: 2rem;
            }
            
            #map {
                height: 500px;
            }
            
            .map-container {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .status-items {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
        }
        
        @media (max-width: 480px) {
            #map {
                height: 400px;
            }
            
            .page-title h2 {
                font-size: 1.8rem;
            }
            
            .info-card {
                padding: 15px;
            }
            
            .info-card i {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
<div class="main-container">
    <?php include "menu.php"; ?>

    <section class="contenedor">
        <div class="page-title">
            <h2><i class="fas fa-map-marked-alt"></i> Mapa GPS del Dron</h2>
            <p>Seguimiento en tiempo real de la ubicación y trayectoria del dron magnético</p>
        </div>
        
        <div class="map-container">
            <div id="map"></div>
        </div>
        
        <div class="info-panel">
            <div class="info-grid">
                <div class="info-card">
                    <i class="fas fa-satellite-dish"></i>
                    <h4>Precisión GPS</h4>
                    <p>± 1.5 metros de exactitud</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-battery-three-quarters"></i>
                    <h4>Nivel de Batería</h4>
                    <p>78% - Duración estimada: 2h 15min</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-signal"></i>
                    <h4>Calidad de Señal</h4>
                    <p>Excelente - 98% de estabilidad</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <h4>Última Actualización</h4>
                    <p id="last-update">Hace 45 segundos</p>
                </div>
            </div>
            
            <div class="status-section">
                <h3 class="status-title">Estado del Sistema</h3>
                <div class="status-items">
                    <div class="status-item online">
                        <div class="status-dot"></div>
                        <span>Dron Conectado</span>
                    </div>
                    <div class="status-item online">
                        <div class="status-dot"></div>
                        <span>GPS Activo</span>
                    </div>
                    <div class="status-item online">
                        <div class="status-dot"></div>
                        <span>Transmisión en Vivo</span>
                    </div>
                    <div class="status-item online">
                        <div class="status-dot"></div>
                        <span>Sensores Operativos</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <p>© 2025 Dron Magnético - Sistema de Monitoreo GPS en Tiempo Real</p>
    </footer>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el mapa
        const map = L.map('map').setView([19.656558, -99.190239], 17);

        // Añadir capa de tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Icono personalizado para el dron
        const dronIcon = L.divIcon({
            className: 'dron-marker',
            html: '<i class="fas fa-drone" style="color: white; font-size: 18px; background: #1abc9c; padding: 10px; border-radius: 50%; border: 3px solid white;"></i>',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        // Marcador del dron
        const dronMarker = L.marker([19.656558, -99.190239], {icon: dronIcon})
            .addTo(map)
            .bindPopup(`
                <div style="min-width: 200px;">
                    <h4 style="color: #1abc9c; margin-bottom: 10px;">
                        <i class="fas fa-drone"></i> Dron Magnético Activo
                    </h4>
                    <p><strong>Ubicación:</strong> TESCI</p>
                    <p><strong>Estado:</strong> En operación</p>
                    <p><strong>Batería:</strong> 78%</p>
                    <p><strong>Altura:</strong> 45 metros</p>
                    <p><strong>Velocidad:</strong> 12 km/h</p>
                </div>
            `)
            .openPopup();

        // Añadir más marcadores
        L.marker([19.656750, -99.190830])
            .addTo(map)
            .bindPopup("<strong>Entrada Principal TESCI</strong><br>Acceso principal al campus universitario");

        L.marker([19.656480, -99.189950])
            .addTo(map)
            .bindPopup("<strong>Edificio A</strong><br>Aulas y laboratorios principales");

        L.marker([19.656250, -99.190350])
            .addTo(map)
            .bindPopup("<strong>Biblioteca</strong><br>Centro de recursos académicos y estudio");

        L.marker([19.657050, -99.189460])
            .addTo(map)
            .bindPopup("<strong>Canchas Deportivas</strong><br>Área recreativa y deportiva");

        // Añadir círculo alrededor del dron para mostrar área de cobertura
        L.circle([19.656558, -99.190239], {
            color: '#1abc9c',
            fillColor: '#1abc9c',
            fillOpacity: 0.1,
            radius: 100
        }).addTo(map);

        // Simular actualización en tiempo real
        setInterval(() => {
            const seconds = Math.floor(Math.random() * 60) + 1;
            document.getElementById('last-update').textContent = `Hace ${seconds} segundos`;
        }, 3000);

        // Forzar redimensionamiento del mapa después de cargar
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    });
</script>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>