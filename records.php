<?php
session_start();
include 'db/connection.php';

// Consulta para obtener los datos de la tabla "pedido"
$query = "
    SELECT 
        ID_Pedido,
        Fecha,
        Metodo_Pago,
        Total,
        Recibido,
        Cambio
    FROM pedido
    ORDER BY Fecha DESC
";
$result = $conection->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarCash - Historial de Ventas</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: rgba(77, 76, 72, 255);
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: white;
            margin: 20px 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(58, 59, 58, 1);
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: black;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .navbar {
            background-color: black !important;
        }

        .navbar .navbar-brand,
        .navbar .nav-link {
            color: white !important;
        }

        .navbar .nav-link.active {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- "BarCash" a la izquierda -->
            <a class="navbar-brand" href="#">BarCash</a>
            
            <!-- Botón colapsable para dispositivos pequeños -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Texto centrado: "HISTORIAL" -->
                <div class="mx-auto text-center">
                    <span class="navbar-text text-white fw-bold fs-4">Historial de Ventas</span>
                </div>
                
                <!-- Elementos alineados a la derecha -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="inventory.php">Inventario |</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Órdenes |</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="records.php">Historial</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">

        <!-- Tabla de Historial -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Fecha</th>
                    <th>Método de Pago</th>
                    <th>Total</th>
                    <th>Monto Recibido</th>
                    <th>Cambio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['ID_Pedido']) . "</td>
                                <td>" . htmlspecialchars($row['Fecha']) . "</td>
                                <td>" . htmlspecialchars($row['Metodo_Pago']) . "</td>
                                <td>$" . number_format($row['Total'], 2) . "</td>
                                <td>$" . number_format($row['Recibido'], 2) . "</td>
                                <td>$" . number_format($row['Cambio'], 2) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay registros de ventas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>