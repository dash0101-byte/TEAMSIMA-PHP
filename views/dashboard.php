<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Dron Magnético</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../public/css/bootstrap.min.css">

    <!-- Fuentes e iconos -->
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
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .dashboard {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 50px;
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
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .card {
            background-color: #fff;
            padding: 30px 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            border-top: 4px solid var(--primary);
            position: relative;
            overflow: hidden;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--primary));
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-icon {
            font-size: 2.8rem;
            margin-bottom: 20px;
            color: var(--primary);
        }
        
        .card h3 {
            margin-bottom: 15px;
            color: var(--dark);
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .card-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--secondary);
        }
        
        .card-unit {
            font-size: 1rem;
            color: #666;
            font-weight: 500;
        }
        
        .card-status {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 10px;
        }
        
        .status-online {
            background-color: rgba(39, 174, 96, 0.1);
            color: #27ae60;
        }
        
        .status-warning {
            background-color: rgba(243, 156, 18, 0.1);
            color: #f39c12;
        }
        
        .status-offline {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }
        
        /* Cards especiales con colores diferentes */
        .card-battery {
            border-top-color: var(--accent);
        }
        
        .card-battery .card-icon {
            color: var(--accent);
        }
        
        .card-temperature {
            border-top-color: var(--warning);
        }
        
        .card-temperature .card-icon {
            color: var(--warning);
        }
        
        .card-status-drone {
            border-top-color: var(--primary);
        }
        
        .card-gps {
            border-top-color: #9b59b6;
        }
        
        .card-gps .card-icon {
            color: #9b59b6;
        }
        
        .card-altitude {
            border-top-color: #34495e;
        }
        
        .card-altitude .card-icon {
            color: #34495e;
        }
        
        /* Panel de control adicional */
        .control-panel {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-top: 30px;
        }
        
        .control-title {
            text-align: center;
            margin-bottom: 25px;
            color: var(--dark);
            font-size: 1.5rem;
        }
        
        .control-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .control-btn {
            padding: 15px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background-color: var(--warning);
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background-color: var(--accent);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #16a085;
            transform: translateY(-2px);
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
            .dashboard {
                margin: 20px auto;
            }
            
            .page-title h2 {
                font-size: 2rem;
            }
            
            .grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .card {
                padding: 25px 20px;
            }
            
            .card-value {
                font-size: 1.8rem;
            }
            
            .control-buttons {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .page-title h2 {
                font-size: 1.8rem;
            }
            
            .grid {
                grid-template-columns: 1fr;
            }
            
            .card {
                padding: 20px 15px;
            }
            
            .card-icon {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>

<div class="main-container">
    <?php include "menu.php"; ?>

    <main>
        <section class="dashboard">
            <div class="page-title">
                <h2><i class="fas fa-tachometer-alt"></i> Panel de Control del Dron</h2>
                <p>Monitoreo en tiempo real del estado y parámetros del dron magnético</p>
            </div>

            <div class="grid">
                <div class="card card-battery">
                    <i class="fas fa-battery-three-quarters card-icon"></i>
                    <h3>Nivel de Batería</h3>
                    <div class="card-value" id="bateria">--</div>
                    <div class="card-unit">porcentaje</div>
                    <div class="card-status status-online" id="battery-status">Cargando</div>
                </div>
                
                <div class="card card-status-drone">
                    <i class="fas fa-drone card-icon"></i>
                    <h3>Estado del Dron</h3>
                    <div class="card-value" id="estado">--</div>
                    <div class="card-unit">operación</div>
                    <div class="card-status status-online" id="drone-status">Conectado</div>
                </div>
                
                <div class="card card-temperature">
                    <i class="fas fa-thermometer-half card-icon"></i>
                    <h3>Temperatura</h3>
                    <div class="card-value" id="temperatura">--</div>
                    <div class="card-unit">°C</div>
                    <div class="card-status status-online" id="temp-status">Normal</div>
                </div>
                
                <div class="card">
                    <i class="fas fa-wind card-icon"></i>
                    <h3>Nivel de CO2</h3>
                    <div class="card-value" id="co2">--</div>
                    <div class="card-unit">PPM</div>
                    <div class="card-status status-online" id="co2-status">Activo</div>
                </div>
                
                <div class="card card-gps">
                    <i class="fas fa-tint card-icon"></i>
                    <h3>Humedad Relativa</h3>
                    <div class="card-value" id="humedad">--</div>
                    <div class="card-unit">%</div>
                    <div class="card-status status-online" id="hum-status">Activo</div>
                </div>
                
                <div class="card card-altitude">
                    <i class="fas fa-tachometer-alt card-icon"></i>
                    <h3>Presión Atmosférica</h3>
                    <div class="card-value" id="presion">--</div>
                    <div class="card-unit">hPa</div>
                    <div class="card-status status-online" id="pres-status">Estable</div>
                </div>
            </div>

            <div class="control-panel">
                <h3 class="control-title"><i class="fas fa-gamepad"></i> Controles del Dron</h3>
                <div class="control-buttons">
                    <button class="control-btn btn-primary">
                        <i class="fas fa-play"></i> Iniciar Vuelo
                    </button>
                    <button class="control-btn btn-warning">
                        <i class="fas fa-pause"></i> Pausar
                    </button>
                    <button class="control-btn btn-success">
                        <i class="fas fa-home"></i> Regresar a Base
                    </button>
                    <button class="control-btn btn-danger">
                        <i class="fas fa-stop"></i> Emergencia
                    </button>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>© 2025 Dron Magnético - Sistema de Control y Monitoreo</p>
    </footer>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
// Actualización de dashboard escaneando desde el PHP Proxy
function actualizarDashboard() {
    fetch('get_datos.php')
        .then(res => res.json())
        .then(data => {
            // Se actualizan los datos si los IDs existen en el HTML
            if(document.getElementById('temperatura')) document.getElementById('temperatura').textContent = data.temperatura;
            if(document.getElementById('estado')) document.getElementById('estado').textContent = data.estado;
            if(document.getElementById('bateria')) document.getElementById('bateria').textContent = data.bateria;
            
            // Datos de los sensores ambientales
            if(document.getElementById('co2')) document.getElementById('co2').textContent = data.co2;
            if(document.getElementById('humedad')) document.getElementById('humedad').textContent = data.humedad;
            if(document.getElementById('presion')) document.getElementById('presion').textContent = data.presion;
            
            actualizarEstados(data);
        })
        .catch(err => console.error('Error al escanear los sensores:', err));
}

function actualizarEstados(data) {
    // Actualizar estados visuales de las píldoras de color
    const tempStatus = document.getElementById('temp-status');
    if(tempStatus && data.temperatura !== '--') {
        const temp = parseFloat(data.temperatura);
        if(temp > 35) {
            tempStatus.className = 'card-status status-offline';
            tempStatus.textContent = 'Crítica';
        } else {
            tempStatus.className = 'card-status status-online';
            tempStatus.textContent = 'Normal';
        }
    }

    const droneStatus = document.getElementById('drone-status');
    if(droneStatus) {
        droneStatus.className = data.estado === 'Conectado' ? 'card-status status-online' : 'card-status status-offline';
        droneStatus.textContent = data.estado;
    }
}

// Escanear datos en tiempo real cada 1 segundo (1000ms)
setInterval(actualizarDashboard, 1000);
actualizarDashboard(); // Ejecuta al cargar la página
</script>

</body>
</html>