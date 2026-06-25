<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "drondb");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$sql = "SELECT * FROM vuelos ORDER BY fecha_inicio DESC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de Vuelos - SIMA</title>

    <link rel="stylesheet" href="../public/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            font-family: 'Poppins', Arial, sans-serif;
            min-height: 100vh;
        }

        .contenedor-vuelos {
            max-width: 1100px;
            margin: 50px auto;
            padding: 25px;
        }

        .panel-vuelos {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(59, 130, 246, 0.25);
            border-radius: 22px;
            padding: 30px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.45);
        }

        .titulo-vuelos {
            font-weight: 800;
            margin-bottom: 5px;
        }

        .subtitulo-vuelos {
            color: #94a3b8;
            margin-bottom: 25px;
        }

        .barra-acciones {
            display: flex;
            gap: 15px;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .buscador {
            position: relative;
            flex: 1;
            min-width: 260px;
        }

        .buscador i {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .buscador input {
            width: 100%;
            padding: 13px 18px 13px 45px;
            border-radius: 50px;
            border: 1px solid rgba(59, 130, 246, 0.35);
            background: #020617;
            color: white;
            outline: none;
        }

        .buscador input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 18px rgba(56, 189, 248, 0.25);
        }

        .btn-pdf {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 50px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-pdf:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.35);
        }

        .tabla-contenedor {
            border-radius: 18px;
            overflow: hidden;
            background: white;
        }

        table {
            margin: 0 !important;
        }

        thead th {
            background: #020617 !important;
            color: white !important;
            padding: 15px !important;
        }

        tbody td {
            padding: 14px !important;
            vertical-align: middle;
        }

        tbody tr:hover {
            background-color: #e0f2fe !important;
        }

        .mensaje-vacio {
            color: #64748b;
            font-weight: 600;
            padding: 20px;
        }

        .contador {
            color: #94a3b8;
            font-size: 0.95rem;
            margin-top: 15px;
        }

        @media print {
            .bob-navbar,
            .barra-acciones,
            .contador {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
            }

            .contenedor-vuelos {
                margin: 0;
                padding: 0;
                max-width: 100%;
            }

            .panel-vuelos {
                box-shadow: none;
                border: none;
                background: white;
                padding: 0;
            }

            .titulo-vuelos,
            .subtitulo-vuelos {
                color: black !important;
                text-align: center;
            }

            tr.oculto {
                display: none !important;
            }

            table {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

<?php include 'menu.php'; ?>

<div class="contenedor-vuelos">

    <div class="panel-vuelos">

        <h2 class="titulo-vuelos">
            <i class="fas fa-plane-departure"></i>
            Registro de Vuelos
        </h2>

        <p class="subtitulo-vuelos">
            Consulta, busca e imprime los vuelos registrados en el sistema SIMA.
        </p>

        <div class="barra-acciones">

            <div class="buscador">
                <i class="fas fa-search"></i>
                <input type="text" id="buscarVuelo" placeholder="Buscar por ID, usuario, fecha o ubicación...">
            </div>

            <button onclick="window.print()" class="btn-pdf">
                <i class="fas fa-file-pdf"></i>
                Descargar PDF
            </button>

        </div>

        <div class="tabla-contenedor">

            <table class="table table-bordered table-striped text-center" id="tablaVuelos">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario ID</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Ubicación Inicio</th>
                        <th>Ubicación Fin</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if($resultado && $resultado->num_rows > 0): ?>

                        <?php while($fila = $resultado->fetch_assoc()): ?>

                            <tr class="fila-vuelo">

                                <td><?= htmlspecialchars($fila['id']); ?></td>
                                <td><?= htmlspecialchars($fila['usuario_id']); ?></td>
                                <td><?= htmlspecialchars($fila['fecha_inicio']); ?></td>
                                <td><?= htmlspecialchars($fila['fecha_fin']); ?></td>
                                <td><?= htmlspecialchars($fila['ubicacion_inicio']); ?></td>
                                <td><?= htmlspecialchars($fila['ubicacion_fin']); ?></td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="mensaje-vacio">
                                No hay vuelos registrados todavía.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <p class="contador" id="contadorVuelos"></p>

    </div>

</div>

<script src="../public/js/bootstrap.bundle.min.js"></script>

<script>
    const buscador = document.getElementById("buscarVuelo");
    const filas = document.querySelectorAll(".fila-vuelo");
    const contador = document.getElementById("contadorVuelos");

    function actualizarContador() {
        let visibles = 0;

        filas.forEach(fila => {
            if (!fila.classList.contains("oculto")) {
                visibles++;
            }
        });

        contador.textContent = "Vuelos visibles: " + visibles;
    }

    buscador.addEventListener("keyup", function () {
        const texto = this.value.toLowerCase();

        filas.forEach(fila => {
            const contenido = fila.textContent.toLowerCase();

            if (contenido.includes(texto)) {
                fila.classList.remove("oculto");
                fila.style.display = "";
            } else {
                fila.classList.add("oculto");
                fila.style.display = "none";
            }
        });

        actualizarContador();
    });

    actualizarContador();
</script>

</body>
</html>