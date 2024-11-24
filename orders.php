<?php
session_start();
include 'db/connection.php';

// Fetch all products
$query = "SELECT * FROM producto";
$result = $conection->query($query);

// Initialize total
$total = 0;

// Handle "Agregar al Pedido" form submission
if (isset($_POST['agregar'])) {
    $idProducto = $_POST['producto'];
    $cantidad = $_POST['cantidad'];

    // Check if the product is already in the cart
    $exists = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['id'] == $idProducto) {
            $item['cantidad'] += $cantidad;
            $exists = true;
            break;
        }
    }

    // If the product is not in the cart, add it
    if (!$exists) {
        $query = "SELECT * FROM producto WHERE ID_Producto = $idProducto";
        $producto = $conection->query($query)->fetch_assoc();
        if ($producto) {
            $_SESSION['carrito'][] = [
                'id' => $producto['ID_Producto'],
                'nombre' => $producto['Nombre'],
                'precio' => $producto['Precio'],
                'cantidad' => $cantidad
            ];
        }
    }
}

// Handle "Eliminar" button form submission
if (isset($_POST['eliminar'])) {
    $indice = $_POST['indice'];
    if (isset($_SESSION['carrito'][$indice])) {
        unset($_SESSION['carrito'][$indice]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindex the array
    }
    header('Location: orders.php');
    exit();
}

// Handle "Calcular Cambio" and "Submit Order"
$cambio = null;
if (isset($_POST['submit_order'])) {
    $recibido = $_POST['recibido'];

    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    if ($recibido < $total) {
        $error = "El monto recibido no puede ser menor al total.";
    } else {
        $cambio = $recibido - $total;

        if (empty($_SESSION['carrito'])) {
            $error = "No hay productos en el pedido.";
        } else {
            $idAdministrador = 1; // Replace with the actual logged-in administrator ID
            $fecha = date('Y-m-d');

            // Insert into `pedido`
            $query = "INSERT INTO pedido (ID_Administrador, Fecha, Metodo_Pago, Total, Recibido, Cambio) 
                      VALUES ($idAdministrador, '$fecha', 'Efectivo', $total, $recibido, $cambio)";
            $conection->query($query);

            $idPedido = $conection->insert_id;

            // Insert into `detalle_pedido`
            foreach ($_SESSION['carrito'] as $item) {
                $query = "INSERT INTO detalle_pedido (ID_Pedido, ID_Producto, Cantidad) 
                          VALUES ($idPedido, {$item['id']}, {$item['cantidad']})";
                $conection->query($query);
            }

            // Clear the cart
            $_SESSION['carrito'] = [];
            $success = "Pedido registrado correctamente. Cambio: $$cambio.";
        }
    }
}
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
            background-color: rgba(77, 76, 72, 255);
            color: white;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }
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
        .top-bar .menu-buttons {
            display: flex;
            gap: 1rem;
        }
        .top-bar button {
            background-color: black;
            color: white;
            border: 2px solid white;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        .top-bar button:hover {
            background-color: white;
            color: black;
        }
        .container {
            margin-top: 40px;
        }
        .form-box {
            background-color: rgba(58, 59, 58, 1);
            padding: 2rem;
            border-radius: 10px;
        }
        table {
            width: 100%;
            margin: 20px auto;
            text-align: center;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
        }
        table th {
            background-color: #444;
            color: white;
        }
        table {
            background-color: #222;
            color: white;
        }
        .btn-primary, .btn-submit {
            background-color: black;
            color: white;
            border: 2px solid white;
        }
        .btn-primary:hover, .btn-submit:hover {
            background-color: white;
            color: black;
        }
        .centered {
            margin: 0 auto;
        }
        .total-row {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="logo">Barcash</div>
        <div class="menu-buttons">
            <button onclick="location.href='dashboard.php'">Menu</button>
            <button onclick="location.href='logout.php'">Logout</button>
        </div>
    </div>

    <div class="container">
        <h1 class="text-center mb-4">Registro de Ventas</h1>

        <!-- Add to Order Form -->
        <form method="POST" class="form-box mb-4">
            <div class="mb-3">
                <label for="producto" class="form-label">Selecciona un producto:</label>
                <select name="producto" id="producto" class="form-select" required>
                    <option value="">-- Seleccionar --</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['ID_Producto'] ?>"><?= $row['Nombre'] ?> - $<?= number_format($row['Precio'], 2) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad:</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
            </div>
            <button type="submit" name="agregar" class="btn btn-primary w-100">Agregar al Pedido</button>
        </form>

        <!-- Sales Table -->
        <div class="form-box centered">
            <table class="table table-dark table-striped">
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
                    <?php if (!empty($_SESSION['carrito'])): ?>
                        <?php foreach ($_SESSION['carrito'] as $index => $item): ?>
                            <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                            <?php $total += $subtotal; ?>
                            <tr>
                                <td><?= $item['nombre'] ?></td>
                                <td><?= $item['cantidad'] ?></td>
                                <td>$<?= number_format($item['precio'], 2) ?></td>
                                <td>$<?= number_format($subtotal, 2) ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="indice" value="<?= $index ?>">
                                        <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No hay productos en el pedido.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3">Total</td>
                        <td colspan="2">$<?= number_format($total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Calculate Change -->
        <form method="POST" class="form-box">
            <div class="mb-3">
                <label for="recibido" class="form-label">Monto recibido:</label>
                <input type="number" name="recibido" id="recibido" class="form-control" min="0" required>
            </div>
            <button type="submit" name="submit_order" class="btn btn-submit w-100">Finalizar Pedido</button>
        </form>

        <!-- Change Output -->
        <?php if (isset($cambio)): ?>
            <div class="alert alert-success mt-3 text-center">Cambio: $<?= number_format($cambio, 2) ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger mt-3 text-center"><?= $error ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success mt-3 text-center"><?= $success ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
