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
    <title>Órdenes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #4e4e4e;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
            color: white;
        }
        .container {
            width: 80%;
            margin: 30px auto;
            background-color: rgba(58, 59, 58, 1);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        label {
            font-size: 14px;
            color: white;
            margin-bottom: 5px;
        }
        select, input {
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            color: #000;
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
        .footer {
            font-weight: bold;
            font-size: 18px;
            text-align: right;
        }
        .navbar {
            background-color: black;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid purple;
        }
        .navbar img {
            height: 40px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }
        .navbar a.active {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <!-- Logo a la izquierda -->
        <div>
            <img src="assets/images/logo.png" alt="Logo">
            <span class="text-white ms-2">BarCash</span>
        </div>
        
        <!-- Opciones del menú -->
        <div>
            <a href="inventory.php">Inventario</a> |
            <a href="orders.php" class="active">Órdenes</a> |
            <a href="records.php">Historial</a>
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

        <form method="POST" action="orders.php">
            <div>
                <label for="metodo_pago">Método de Pago:</label>
                <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                </select>
            </div>
            <div>
                <label for="recibido">Monto Recibido:</label>
                <input type="number" name="recibido" id="recibido" class="form-control" min="0" required>
            </div>
            <div>
                <button type="submit" name="finalizar" class="btn btn-success mt-3">Finalizar Pedido</button>
            </div>
        </form>

        <?php
        if (isset($_POST['eliminar'])) {
            $indice = $_POST['indice'];
            unset($_SESSION['carrito'][$indice]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
            header('Location: orders.php');
            exit();
        }
        if (isset($_POST['finalizar'])) {
            $metodo_pago = $_POST['metodo_pago'];
            $recibido = $_POST['recibido'];

            if ($recibido < $total) {
                echo "<script>alert('El monto recibido no puede ser menor al total.');</script>";
                exit();
            }

            $cambio = $recibido - $total;
            $idAdministrador = 1;
            $fecha = date('Y-m-d');

            $query = "INSERT INTO pedido (ID_Administrador, Fecha, Metodo_Pago, Total, Recibido, Cambio) 
                      VALUES ($idAdministrador, '$fecha', '$metodo_pago', $total, $recibido, $cambio)";
            $conection->query($query);

            $idPedido = $conection->insert_id;

            foreach ($_SESSION['carrito'] as $item) {
                $query = "INSERT INTO detalle_pedido (ID_Pedido, ID_Producto, Cantidad) 
                          VALUES ($idPedido, {$item['id']}, {$item['cantidad']})";
                $conection->query($query);
            }

            // Limpiar el carrito
            $_SESSION['carrito'] = [];

            echo "<script>alert('Pedido registrado correctamente.'); window.location = 'orders.php';</script>";
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>