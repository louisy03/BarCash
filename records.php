<?php
session_start();
include 'db/connection.php'; 


$query = "SELECT * FROM producto";
$result = $conection->query($query);


$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ventas</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin: 20px 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            color: #34495e;
            margin-bottom: 5px;
        }

        select, input {
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .total {
            font-weight: bold;
            font-size: 18px;
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
            font-size: 18px;
            color: #3498db;
        }

        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Ventas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="inventory.php">Inventario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Órdenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="records.php">Historial</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Registro de Ventas</h1>

        
        <form method="POST" action="orders.php">
            <div>
                <label for="producto">Selecciona un producto:</label>
                <select name="producto" id="producto" class="form-select" required>
                    <option value="">-- Seleccionar --</option>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <option value="<?= $row['ID_Producto'] ?>">
                            <?= $row['Nombre'] ?> - $<?= number_format($row['Precio'], 2) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
            </div>
            <div>
                <button type="submit" name="agregar" class="btn btn-success mt-3">Agregar al Pedido</button>
            </div>
        </form>

        <hr>

        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                if (!isset($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = [];
                }

                
                if (isset($_POST['agregar'])) {
                    $idProducto = $_POST['producto'];
                    $cantidad = $_POST['cantidad'];

                    
                    $existe = false;
                    foreach ($_SESSION['carrito'] as &$item) {
                        if ($item['id'] == $idProducto) {
                            $item['cantidad'] += $cantidad;
                            $existe = true;
                            break;
                        }
                    }

                    
                    if (!$existe) {
                        $query = "SELECT * FROM producto WHERE ID_Producto = $idProducto";
                        $producto = $conection->query($query)->fetch_assoc();
                        $_SESSION['carrito'][] = [
                            'id' => $producto['ID_Producto'],
                            'nombre' => $producto['Nombre'],
                            'precio' => $producto['Precio'],
                            'cantidad' => $cantidad
                        ];
                    }
                }

                
                if (!empty($_SESSION['carrito'])) {
                    foreach ($_SESSION['carrito'] as $index => $item) {
                        $subtotal = $item['precio'] * $item['cantidad'];
                        $total += $subtotal;
                        echo "<tr>
                            <td>{$item['nombre']}</td>
                            <td>{$item['cantidad']}</td>
                            <td>$" . number_format($item['precio'], 2) . "</td>
                            <td>$" . number_format($subtotal, 2) . "</td>
                            <td>
                                <form method='POST' action='orders.php'>
                                    <input type='hidden' name='indice' value='{$index}'>
                                    <button type='submit' name='eliminar' class='btn btn-danger'>Eliminar</button>
                                </form>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay productos en el carrito</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="footer">
            <span>Total: $<?= number_format($total, 2) ?></span>
        </div>

        <?php
        
        if (isset($_POST['eliminar'])) {
            $indice = $_POST['indice'];
            unset($_SESSION['carrito'][$indice]);
            
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
            header('Location: orders.php'); 
            exit();
        }
        ?>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>