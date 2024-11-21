<!-- File: add_ingredient.php -->
<?php
session_start();
include 'db/connection.php';

// Redirect if not logged in
if (!isset($_SESSION['id_administrador'])) {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $stock = $_POST['stock'];

    // Insert the new ingredient into the database
    $stmt = $conection->prepare("INSERT INTO Ingrediente (Nombre, Stock) VALUES (?, ?)");
    $stmt->bind_param("si", $nombre, $stock);

    if ($stmt->execute()) {
        $success = "Ingrediente agregado exitosamente.";
    } else {
        $error = "Error al agregar el ingrediente: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Ingrediente</title>
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
            max-width: 400px;
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
        <h3>Agregar Ingrediente</h3>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Ingrediente</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <button type="submit" class="btn w-100">Agregar</button>
        </form>
        <button class="btn mt-3 w-100" onclick="location.href='ingredients.php'">Volver a Ingredientes</button>
    </div>
</body>
</html>
