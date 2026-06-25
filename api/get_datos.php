<?php
header('Content-Type: application/json');

// La IP de tu dron (ESP32)
$esp32_url = "http://192.168.4.1/data";

// Silenciamos los warnings con '@' por si el dron está apagado
$esp32_data = @file_get_contents($esp32_url);

if ($esp32_data !== false) {
    // Si el dron responde, decodificamos su JSON
    $sensores = json_decode($esp32_data, true);
    
    // Armamos la respuesta para tu Dashboard
    echo json_encode([
        'temperatura' => $sensores['temp'] ?? '--',
        'co2'         => $sensores['co2'] ?? '--',
        'humedad'     => $sensores['hum'] ?? '--',
        'presion'     => $sensores['pres'] ?? '--',
        // Puedes dejar datos fijos o de base de datos para los demás:
        'bateria'     => 100, 
        'estado'      => 'Conectado',
        'gps'         => 8,
        'altura'      => 15
    ]);
} else {
    // Si el ESP32 no está conectado al WiFi, mandamos datos vacíos
    echo json_encode([
        'temperatura' => '--',
        'co2'         => '--',
        'humedad'     => '--',
        'presion'     => '--',
        'bateria'     => '--',
        'estado'      => 'Desconectado',
        'gps'         => '--',
        'altura'      => '--'
    ]);
}
?>