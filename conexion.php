<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "drondb";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error de Conexión</title>
        <!-- Bootstrap -->
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <!-- BobStrap -->
        <link rel="stylesheet" href="CSS/style.css">
        <!-- Tus estilos -->
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="bob-container bob-mt-lg">
            <div class="bob-alert bob-alert-danger bob-shadow-sm">
                <h3 class="bob-text-danger bob-bold">Error de conexión</h3>
                <p>No se pudo conectar a la base de datos.</p>
                <p><strong>Detalle técnico:</strong> <?php echo $conn->connect_error; ?></p>
            </div>
        </div>
    </body>
    </html>
    <?php
    die(); // Detener ejecución si hay error
}
?>
