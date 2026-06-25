<?php
session_start();

// BASE DE DATOS DE PRODUCTOS
// (Asegúrate de que las extensiones .png o .jpg coincidan con tus archivos reales)
$productos = [
    'scout' => [
        'nombre' => 'SIMA Scout',
        'etiqueta' => 'Para Principiantes',
        'precio' => '$499 USD',
        'img' => 'img/dron_basico.png', // Ojo: verifica si es .jpg o .png en tu carpeta
        'desc' => 'El compañero perfecto para iniciarse en el monitoreo aéreo. Su diseño plegable y ligero permite llevarlo a cualquier lugar sin complicaciones. Ideal para inspecciones rápidas y fotografía básica.',
        'specs' => [
            'Resolución' => '1080p HD',
            'Tiempo de Vuelo' => '25 min',
            'Alcance' => '2 km',
            'Peso' => '249g',
            'GPS' => 'Estándar'
        ]
    ],
    'guardian' => [
        'nombre' => 'SIMA Guardian',
        'etiqueta' => 'Uso Profesional',
        'precio' => '$1,299 USD',
        'img' => 'img/dron_pro.png',
        'desc' => 'Potencia y precisión para misiones críticas. Equipado con visión nocturna y sensores térmicos, es la herramienta definitiva para seguridad privada y monitoreo de infraestructura.',
        'specs' => [
            'Resolución' => '4K + Térmica',
            'Tiempo de Vuelo' => '45 min',
            'Alcance' => '8 km',
            'Sensores' => 'Omnidireccionales',
            'IA' => 'ActiveTrack 4.0'
        ]
    ],
    'titan' => [
        'nombre' => 'SIMA Titan',
        'etiqueta' => 'Industrial',
        'precio' => '$3,500 USD',
        'img' => 'img/dron_ind.png',
        'desc' => 'Diseñado para las condiciones más extremas. Soporta lluvia, polvo y vientos fuertes. Su capacidad de carga lo hace ideal para transporte ligero, agricultura y topografía.',
        'specs' => [
            'Carga Útil' => '5 kg',
            'Tiempo de Vuelo' => '60+ min',
            'Resistencia' => 'IP55 (Lluvia)',
            'Mapeo' => 'LiDAR Integrado',
            'Conexión' => '4G LTE'
        ]
    ]
];

// Obtener modelo actual
$modelo_actual = isset($_GET['modelo']) ? $_GET['modelo'] : 'scout';
// Si el modelo no existe, volvemos al default
if (!array_key_exists($modelo_actual, $productos)) {
    $modelo_actual = 'scout';
}
$producto = $productos[$modelo_actual];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $producto['nombre'] ?> | SIMA Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../public/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Estilos específicos para hacer robusta la ficha de producto */
        .breadcrumb-area { background: #f8f9fa; padding: 15px 0; font-size: 0.9rem; }
        .breadcrumb a { color: var(--text-light); text-decoration: none; }
        .breadcrumb .active { color: var(--primary); font-weight: 600; }
        
        .product-image-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            text-align: center;
            border: 1px solid #eee;
        }
        
        .main-img {
            max-height: 400px;
            object-fit: contain;
            transition: transform 0.3s;
        }
        .main-img:hover { transform: scale(1.05); }

        .product-info h1 { font-size: 2.5rem; font-weight: 800; color: var(--primary); }
        .product-tag { 
            background: var(--accent); color: white; 
            padding: 5px 15px; border-radius: 50px; 
            font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
            display: inline-block; margin-bottom: 15px;
        }
        
        .price-box {
            font-size: 2.5rem; color: var(--primary); font-weight: 700;
            margin: 20px 0; border-bottom: 1px solid #eee; padding-bottom: 20px;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .feature-item {
            display: flex; align-items: center; gap: 10px;
            font-size: 0.95rem; color: var(--text-main);
        }
        .feature-item i { color: var(--success); }

        .btn-buy-large {
            width: 100%; padding: 18px; font-size: 1.2rem;
            background: var(--primary); color: white;
            border: none; border-radius: 12px; font-weight: 700;
            transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .btn-buy-large:hover { background: var(--accent); transform: translateY(-3px); }

        /* Sección de Detalles Extra */
        .details-section { margin-top: 60px; padding-top: 40px; border-top: 1px solid #eee; }
        .specs-row {
            display: flex; justify-content: space-between;
            padding: 15px 0; border-bottom: 1px solid #f1f1f1;
        }
        .specs-row:last-child { border-bottom: none; }
        .specs-label { font-weight: 600; color: var(--primary); }

        /* Productos Relacionados */
        .related-card {
            border: 1px solid #eee; border-radius: 15px;
            padding: 20px; text-align: center; transition: 0.3s;
            background: white;
        }
        .related-card:hover { box-shadow: var(--shadow); transform: translateY(-5px); }
        .related-img { height: 120px; object-fit: contain; margin-bottom: 15px; }
    </style>
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="breadcrumb-area">
        <div class="container">
            <span class="breadcrumb">
                <a href="index.php">Inicio</a> &nbsp; / &nbsp; 
                <a href="index.php#modelos">Catálogo</a> &nbsp; / &nbsp; 
                <span class="active"><?= $producto['nombre'] ?></span>
            </span>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="product-image-container">
                    <img src="<?= $producto['img'] ?>" alt="<?= $producto['nombre'] ?>" class="img-fluid main-img">
                </div>
            </div>

            <div class="col-lg-5">
                <div class="product-info ps-lg-4">
                    <span class="product-tag"><?= $producto['etiqueta'] ?></span>
                    <h1><?= $producto['nombre'] ?></h1>
                    
                    <div class="price-box"><?= $producto['precio'] ?></div>
                    
                    <p class="mb-4 text-muted"><?= $producto['desc'] ?></p>

                    <div class="feature-grid">
                        <?php foreach(array_slice($producto['specs'], 0, 4) as $key => $val): ?>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i> 
                            <span><strong><?= $key ?>:</strong> <?= $val ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-grid gap-3">
                        <button class="btn-buy-large">
                            <i class="fas fa-shopping-cart"></i> Añadir al Carrito
                        </button>
                        <a href="registro.php" class="btn btn-outline-dark py-3" style="border-radius:12px; font-weight:600;">
                            <i class="fas fa-file-invoice"></i> Solicitar Ficha Técnica
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row details-section">
            <div class="col-md-8 mx-auto">
                <h3 class="text-center mb-4 font-weight-bold">Especificaciones Completas</h3>
                <div class="card border-0 shadow-sm p-4">
                    <?php foreach($producto['specs'] as $key => $val): ?>
                    <div class="specs-row">
                        <span class="specs-label"><?= $key ?></span>
                        <span class="text-muted"><?= $val ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-4">
            <h3 class="mb-4">También te podría interesar</h3>
            <div class="row">
                <?php foreach($productos as $key => $p): ?>
                    <?php if($key !== $modelo_actual): // No mostrar el producto actual ?>
                    <div class="col-md-6 mb-3">
                        <div class="related-card">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <img src="<?= $p['img'] ?>" class="img-fluid related-img" alt="<?= $p['nombre'] ?>">
                                </div>
                                <div class="col-8 text-start">
                                    <h5 class="mb-1 fw-bold"><?= $p['nombre'] ?></h5>
                                    <p class="text-primary fw-bold mb-2"><?= $p['precio'] ?></p>
                                    <a href="dron_producto.php?modelo=<?= $key ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <footer>
        <p>© 2025 SIMA Corporation - Tecnología de Altura</p>
    </footer>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>