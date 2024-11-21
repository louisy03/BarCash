<?php
session_start();
include 'db/connection.php';

// Redirect if not logged in
if (!isset($_SESSION['id_administrador'])) {
    header("Location: index.php");
    exit;
}

// Fetch beverages with their ingredients and quantities
$beverages = $conection->query("
    SELECT 
        p.Nombre AS Bebida, 
        p.Descripcion, 
        p.Precio, 
        GROUP_CONCAT(CONCAT(i.Nombre, ' (', pi.Cantidad, ')') SEPARATOR '<br>') AS Ingredientes
    FROM Producto p
    LEFT JOIN PRODUCTO_INGREDIENTE pi ON p.ID_Producto = pi.ID_Producto
    LEFT JOIN Ingrediente i ON pi.ID_Ingrediente = i.ID_Ingrediente
    GROUP BY p.ID_Producto
    ORDER BY p.ID_Producto DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Bebidas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            width: 40px;
            height: auto;
        }

        .top-bar h1 {
            margin: 0;
            font-size: 1.5rem;
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

        /* Beverage List Box */
        .beverage-box {
            background-color: rgba(58, 59, 58, 1); /* Dark box */
            color: white;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-y: auto;
            max-height: 400px; /* Scrollable if content exceeds */
        }

        .beverage-box table {
            width: 100%;
            text-align: left;
        }

        .beverage-box th, .beverage-box td {
            padding: 0.5rem;
        }

        /* Operation Buttons */
        .operation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        /* Button Colors */
        .operation-buttons .add-button {
            background-color: green;
            color: white;
            border: 2px solid white;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .operation-buttons .edit-button {
            background-color: orange;
            color: white;
            border: 2px solid white;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .operation-buttons .delete-button {
            background-color: red;
            color: white;
            border: 2px solid white;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .operation-buttons .view-button {
            background-color: blue;
            color: white;
            border: 2px solid white;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .operation-buttons button:hover {
            background-color: white;
            color: black;
        }

        /* Add Background Color to Body */
        body {
            background-color: rgba(77, 76, 72, 1); /* Add background color */
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <!-- Left: Logo and Title -->
        <div>
            <img src="assets/images/logo.png" alt="Logo" class="logo">
            <span>Barcash</span>
        </div>

        <!-- Center: Page Title -->
        <h1>Bebidas</h1>

        <!-- Right: Navigation Buttons -->
        <div class="nav-buttons">
            <a href="dashboard.php">Inicio</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <!-- Beverage List -->
    <div class="container">
        <div class="beverage-box">
            <h3>Lista de Bebidas</h3>
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Ingredientes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $beverages->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['Bebida']; ?></td>
                            <td><?php echo $row['Descripcion']; ?></td>
                            <td><?php echo $row['Precio']; ?></td>
                            <td><?php echo $row['Ingredientes'] ?: 'N/A'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Operation Buttons -->
        <div class="operation-buttons">
            <button class="add-button" onclick="location.href='add_beverage.php'">Agregar</button>
            <button class="edit-button" onclick="location.href='edit_beverage.php'">Editar</button>
            <button class="delete-button" onclick="location.href='delete_beverage.php'">Eliminar</button>
            <button class="view-button" onclick="location.href='ingredients.php'">Visualizar Ingredientes</button>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
