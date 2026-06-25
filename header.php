<header class="bob-navbar" style="background: linear-gradient(90deg, #1e272e, #485460); box-shadow:0 4px 10px rgba(0,0,0,0.4);">
    <div class="bob-container d-flex align-items-center justify-content-between" style="padding: 20px 30px; max-width: 1200px; margin: 0 auto; flex-wrap:wrap;">
        <!-- Logo / Título -->
        <div class="bob-logo">
            <h1 style="margin: 0; font-size: 2rem; color: #00d8ff; font-weight: 800; letter-spacing: 1px;">Dron Magnético</h1>
        </div>
        <!-- Menú -->
        <nav class="bob-navbar-menu">
            <ul class="d-flex align-items-center" style="list-style: none; margin: 0; padding: 0; gap: 20px; flex-wrap:wrap;">
                <li><a href="index.php" class="menu-btn">Inicio</a></li>
                <li><a href="dashboard.php" class="menu-btn">Dashboard</a></li>
                <li><a href="mapa.php" class="menu-btn">Mapa GPS</a></li>
                <li><a href="comentarios.php" class="menu-btn">Comentarios</a></li>
                <?php if(isset($_SESSION["usuario"])): ?>
                    <li><span style="color:#f5f6fa; font-weight:500; font-size:1rem;">Bienvenido, <?= htmlspecialchars($_SESSION["usuario"]); ?></span></li>
                    <li><a href="logout.php" class="logout-btn">Salir</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
