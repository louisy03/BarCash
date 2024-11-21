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
    $id = $_POST['id_ingrediente'];
    $nombre = $_POST['nombre'];
    $stock = $_POST['stock'];

    // Update the ingredient in the database
    $stmt = $conection->prepare("UPDATE Ingrediente SET Nombre = ?, Stock = ? WHERE ID_Ingrediente = ?");
    $stmt->bind_param("sii", $nombre, $stock, $id);

    if ($stmt->execute()) {
        $success = "Ingrediente actualizado exitosamente.";
    } else {
        $error = "Error al actualizar el ingrediente: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ingrediente</title>
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
            transition: background-color 0.3s, color 0.3s;
        }
        .form-box button:hover {
            background-color: white;
            color: black;
        }
    </style>
    <script>
        function updateName() {
            var select = document.getElementById("id_ingrediente");
            var input = document.getElementById("nombre");
            var selectedOption = select.options[select.selectedIndex];
            input.value = selectedOption.text;
        }
    </script>
</head>
<body>
    <div class="form-box">
        <h3>Editar Ingrediente</h3>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="id_ingrediente" class="form-label">Seleccione Ingrediente</label>
                <select class="form-control" id="id_ingrediente" name="id_ingrediente" onchange="updateName()" required>
                    <option value="">Seleccione...</option>
                    <?php while ($row = $ingredients->fetch_assoc()): ?>
                        <option value="<?php echo $row['ID_Ingrediente']; ?>"><?php echo $row['Nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nuevo Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre">
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Nuevo Stock</label>
                <input type="number" class="form-control" id="stock" name="stock">
            </div>
            <button type="submit" class="btn w-100">Actualizar</button>
        </form>
        <button class="btn mt-3 w-100" onclick="location.href='ingredients.php'">Volver a Ingredientes</button>
    </div>
</body>
</html>
