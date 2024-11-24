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
        /* Top Bar Styling */
        .top-bar {
            background-color: black;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 1rem;
        }

        .top-bar .logo {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .top-bar .nav-buttons a {
            color: white;
            text-decoration: none;
            margin-left: 1rem;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .top-bar .nav-buttons a:hover {
            text-decoration: underline;
        }

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
            margin-top: 20px;
            border-collapse: collapse;
            background-color: rgba(58, 59, 58, 1); /* Dark background */
            color: white; /* White text */
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2); /* Subtle white borders */
        }

        th {
            background-color: rgba(0, 0, 0, 0.8); /* Dark header */
            color: white;
        }

        tr:nth-child(even) {
            background-color: rgba(77, 76, 72, 0.5); /* Slightly lighter for even rows */
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Subtle hover effect */
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="logo">BarCash</div>
        <div class="nav-buttons">
            <a href="dashboard.php">Inicio</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <h1>Historial de Ventas</h1>

        <!-- Tabla de Historial -->
        <table class="table table-dark table-striped">
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
