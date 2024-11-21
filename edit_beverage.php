<?php
session_start();
include 'db/connection.php';

// Redirect if not logged in
if (!isset($_SESSION['id_administrador'])) {
    header("Location: index.php");
    exit;
}

// Fetch beverages for the dropdown
$beverages = $conection->query("SELECT ID_Producto, Nombre, Descripcion, Precio FROM Producto");

// Fetch all ingredients
$ingredients = $conection->query("SELECT ID_Ingrediente, Nombre FROM Ingrediente");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_producto = $_POST['id_producto'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $added_ingredients = $_POST['ingredientes'] ?? [];
    $quantities = $_POST['cantidades'] ?? [];
    $removed_ingredients = $_POST['removed_ingredients'] ?? [];

    // Start a transaction
    $conection->begin_transaction();

    try {
        // Update the beverage in the Producto table
        $stmt = $conection->prepare("UPDATE Producto SET Nombre = ?, Descripcion = ?, Precio = ? WHERE ID_Producto = ?");
        $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $id_producto);
        if (!$stmt->execute()) {
            throw new Exception("Error updating beverage: " . $stmt->error);
        }
        $stmt->close();

        // Remove ingredients from the beverage
        if (!empty($removed_ingredients)) {
            $stmt = $conection->prepare("DELETE FROM PRODUCTO_INGREDIENTE WHERE ID_Producto = ? AND ID_Ingrediente = ?");
            foreach ($removed_ingredients as $id_ingrediente) {
                $stmt->bind_param("ii", $id_producto, $id_ingrediente);
                if (!$stmt->execute()) {
                    throw new Exception("Error removing ingredient: " . $stmt->error);
                }
            }
            $stmt->close();
        }

        // Add or update ingredients for the beverage
        if (!empty($added_ingredients)) {
            $stmt = $conection->prepare("INSERT INTO PRODUCTO_INGREDIENTE (ID_Producto, ID_Ingrediente, Cantidad)
                                         VALUES (?, ?, ?)
                                         ON DUPLICATE KEY UPDATE Cantidad = ?");
            foreach ($added_ingredients as $index => $id_ingrediente) {
                $cantidad = $quantities[$index];
                $stmt->bind_param("iidi", $id_producto, $id_ingrediente, $cantidad, $cantidad);
                if (!$stmt->execute()) {
                    throw new Exception("Error adding or updating ingredient: " . $stmt->error);
                }
            }
            $stmt->close();
        }

        // Commit transaction
        $conection->commit();
        $success = "La bebida se actualizó correctamente.";
    } catch (Exception $e) {
        // Rollback on error
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
    <title>Editar Bebida</title>
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
            max-width: 600px;
        }
    </style>
    <script>
        async function updateBeverageData() {
            const dropdown = document.getElementById("id_producto");
            const selectedOption = dropdown.options[dropdown.selectedIndex];
            const idProducto = selectedOption.value;

            // Populate the form with beverage data
            document.getElementById("nombre").value = selectedOption.getAttribute("data-nombre");
            document.getElementById("descripcion").value = selectedOption.getAttribute("data-descripcion");
            document.getElementById("precio").value = selectedOption.getAttribute("data-precio");

            // Fetch and display the ingredients
            if (idProducto) {
                const response = await fetch(`fetch_ingredients.php?id_producto=${idProducto}`);
                const ingredients = await response.json();
                const container = document.getElementById("current-ingredients");
                container.innerHTML = "";

                ingredients.forEach(ingredient => {
                    const div = document.createElement("div");
                    div.className = "d-flex mb-2";
                    div.innerHTML = `
                        <span class="me-2">${ingredient.Nombre} (${ingredient.Cantidad})</span>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeIngredient(this, ${ingredient.ID_Ingrediente})">Eliminar</button>
                    `;
                    container.appendChild(div);
                });
            }
        }

        // Remove an ingredient from the list
        function removeIngredient(button, idIngrediente) {
            const container = document.getElementById("removed-ingredients");
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "removed_ingredients[]";
            input.value = idIngrediente;
            container.appendChild(input);
            button.parentElement.remove();
        }

        // Add a new ingredient input row
        function addIngredient() {
            const container = document.getElementById("new-ingredients-container");
            const row = document.createElement("div");
            row.className = "d-flex mb-2";
            row.innerHTML = `
                <select name="ingredientes[]" class="form-control me-2" required>
                    <option value="">Seleccione un ingrediente</option>
                    <?php while ($row = $ingredients->fetch_assoc()): ?>
                        <option value="<?php echo $row['ID_Ingrediente']; ?>"><?php echo $row['Nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="number" step="0.01" name="cantidades[]" class="form-control me-2" placeholder="Cantidad" required>
                <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">X</button>
            `;
            container.appendChild(row);
        }
    </script>
</head>
<body>
    <div class="form-box">
        <h3>Editar Bebida</h3>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" id="removed-ingredients" name="removed_ingredients[]" value="">
            <div class="mb-3">
                <label for="id_producto" class="form-label">Seleccione Bebida</label>
                <select class="form-control" id="id_producto" name="id_producto" onchange="updateBeverageData()" required>
                    <option value="">Seleccione...</option>
                    <?php while ($row = $beverages->fetch_assoc()): ?>
                        <option 
                            value="<?php echo $row['ID_Producto']; ?>"
                            data-nombre="<?php echo htmlspecialchars($row['Nombre']); ?>"
                            data-descripcion="<?php echo htmlspecialchars($row['Descripcion']); ?>"
                            data-precio="<?php echo $row['Precio']; ?>"
                            <?php echo isset($selected_beverage) && $selected_beverage['ID_Producto'] == $row['ID_Producto'] ? 'selected' : ''; ?>>
                            <?php echo $row['Nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="" required>
            </div>

            <div class="mb-3">
                <h5>Ingredientes Actuales</h5>
                <div id="current-ingredients">
                    <p>No hay ingredientes actuales.</p>
                </div>
                <div id="removed-ingredients"></div>
            </div>

            <div class="mb-3">
                <h5>Agregar Nuevos Ingredientes</h5>
                <div id="new-ingredients-container"></div>
                <button type="button" class="btn btn-primary" onclick="addIngredient()">Agregar Ingrediente</button>
            </div>

            <button type="submit" class="btn w-100">Actualizar</button>
        </form>
        <button class="btn mt-3 w-100" onclick="location.href='beverages.php'">Volver a Bebidas</button>
    </div>
</body>
</html>
