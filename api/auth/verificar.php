<?php

$conn = new mysqli("localhost", "root", "", "drondb");

if ($conn->connect_error) {
    die("Error de conexión");
}

$mensaje = "
<div class='alert alert-success'>

    <h4>Gracias por registrarte en SIMA</h4>

    <p>
        Tu cuenta fue creada correctamente.
    </p>

    <p>
        Ya puedes iniciar sesión en el sistema.
    </p>

    <br>

    <a href='login.php' class='btn btn-primary'>
        Iniciar sesión
    </a>

</div>";
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Registro exitoso</title>

<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

<style>

body{
    background:#f4f6f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    font-family:Arial;
}

.card{
    width:400px;
    padding:30px;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
    background:white;
    text-align:center;
}

a{
    text-decoration:none;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="card">

    <h2>SIMA</h2>

    <br>

    <?= $mensaje ?>

</div>

</body>

</html>