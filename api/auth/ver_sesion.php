<?php
session_start();
echo "<h1>🕵️ Datos de tu Sesión Actual</h1>";
echo "<pre style='background:#f0f0f0; padding:20px; font-size:16px;'>";
var_dump($_SESSION);
echo "</pre>";

echo "<hr>";

if (!isset($_SESSION['rol'])) {
    echo "<h2 style='color:red'>❌ ERROR CRÍTICO: No existe el 'rol' en la sesión.</h2>";
    echo "<p>Causa: Tu archivo <b>procesar_login.php</b> no está guardando el rol o no has cerrado sesión.</p>";
} elseif ($_SESSION['rol'] === 'admin') {
    echo "<h2 style='color:green'>✅ ÉXITO: Eres ADMIN.</h2>";
    echo "<p>Si ves esto, el problema está en <b>menu.php</b> (quizás un espacio extra o error de escritura).</p>";
} else {
    echo "<h2 style='color:orange'>⚠️ ATENCIÓN: Tu rol es '" . $_SESSION['rol'] . "'</h2>";
    echo "<p>Necesitas entrar a phpMyAdmin y cambiar tu rol a <b>admin</b> (en minúsculas).</p>";
}
?>