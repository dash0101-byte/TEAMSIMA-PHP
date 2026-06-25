<?php

$conexion = new mysqli("localhost", "root", "", "drondb");

if($conexion->connect_error){
    die("Error de conexión");
}

$usuario_id = $_POST['usuario_id'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$ubicacion_inicio = $_POST['ubicacion_inicio'];
$ubicacion_fin = $_POST['ubicacion_fin'];

$sql = "INSERT INTO vuelos
(usuario_id, fecha_inicio, fecha_fin, ubicacion_inicio, ubicacion_fin)

VALUES
('$usuario_id','$fecha_inicio','$fecha_fin','$ubicacion_inicio','$ubicacion_fin')";

if($conexion->query($sql) === TRUE){

    header("Location: vuelos.php");

}else{

    echo "Error: " . $conexion->error;

}

$conexion->close();

?>