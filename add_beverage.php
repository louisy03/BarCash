<!-- File: add_beverage.php -->
<?php
session_start();
include 'db/connection.php';

// Redirect if not logged in
if (!isset($_SESSION['id_administrador'])) {
    header("Location: index.php");
    exit;
}

// Fetch ingredients for the dropdown
$ingredients = $conection->query("SELECT ID_Ingrediente, Nombre FROM Ingrediente");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = !empty($_POST['descripcion']) ? $_POST['descripcion'] : null;
    $precio = $_POST['precio'];
    $ingredientes = $_POST['ingredientes']; // Array of ingredient IDs
    $cantidades = $_POST['cantidades']; // Array of ingredient quantities

    // Start transaction
    $conection->begin_transaction();

    try {
        // Insert the new beverage into the Producto table
        $stmt = $conection->prepare("INSERT INTO Producto (Nombre, Descripcion, Precio) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $nombre, $descripcion, $precio);

        if (!$stmt->execute()) {
            throw new Exception("Error adding product: " . $stmt->error);
        }

        $id_producto = $stmt->insert_id; // Get the ID of the new product
        $stmt->close();

        // Insert related ingredients into PRODUCTO_INGREDIENTE table
        $stmt = $conection->prepare("INSERT INTO PRODUCTO_INGREDIENTE (ID_Producto, ID_Ingrediente, Cantidad) VALUES (?, ?, ?)");
        foreach ($ingredientes as $index => $id_ingrediente) {
            $cantidad = $cantidades[$index];
            $stmt->bind_param("iid", $id_producto, $id_ingrediente, $cantidad);
            if (!$stmt->execute()) {
                throw new Exception("Error adding product-ingredient relation: " . $stmt->error);
            }
        }
        $stmt->close();

        // Commit transaction
        $conection->commit();
        $success = "Beverage added successfully.";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conection->rollback();
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Bebida</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(77, 76, 72, 255);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-box {
            background-color: rgba(58, 59, 58, 1);
            padding: 2rem;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
        }
        .form-box button {
            background-color: black;
            color: white;
            border: 2px solid white;
        }
        .form-box button:hover {
            background-color: white;
            color: black;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h3>Agregar Bebida</h3>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Bebida</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ingredientes</label>
                <div id="ingredients-container">
                    <div class="d-flex mb-2">
                        <select name="ingredientes[]" class="form-control me-2">
                            <option value="">Seleccione un ingrediente</option>
                            <?php while ($row = $ingredients->fetch_assoc()): ?>
                                <option value="<?php echo $row['ID_Ingrediente']; ?>"><?php echo $row['Nombre']; ?></option>
                            <?php endwhile; ?>
                        </select>
                        <input type="number" step="0.01" name="cantidades[]" class="form-control me-2" placeholder="Cantidad">
                        <button type="button" class="btn btn-danger" onclick="removeIngredient(this)">X</button>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="addIngredient()">Agregar Ingrediente</button>
            </div>
            <button type="submit" class="btn w-100">Agregar</button>
        </form>
        <button class="btn mt-3 w-100" onclick="location.href='beverages.php'">Volver a Bebidas</button>
    </div>

    <script>
        function addIngredient() {
            const container = document.getElementById('ingredients-container');
            const newRow = document.createElement('div');
            newRow.className = 'd-flex mb-2';
            newRow.innerHTML = `
                <select name="ingredientes[]" class="form-control me-2">
                    <option value="">Seleccione un ingrediente</option>
                    <?php
                    $ingredients->data_seek(0); // Reset ingredient pointer for dynamic content
                    while ($row = $ingredients->fetch_assoc()): ?>
                        <option value="<?php echo $row['ID_Ingrediente']; ?>"><?php echo $row['Nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="number" step="0.01" name="cantidades[]" class="form-control me-2" placeholder="Cantidad">
                <button type="button" class="btn btn-danger" onclick="removeIngredient(this)">X</button>
            `;
            container.appendChild(newRow);
        }

        function removeIngredient(button) {
            button.parentElement.remove();
        }
    </script>
</body>
</html>
