<!-- File: delete_beverage.php -->
<?php
session_start();
include 'db/connection.php';

// Redirect if not logged in
if (!isset($_SESSION['id_administrador'])) {
    header("Location: index.php");
    exit;
}

// Fetch beverages for the dropdown
$beverages = $conection->query("SELECT ID_Producto, Nombre FROM Producto");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_producto = $_POST['id_producto'];

    // Start transaction
    $conection->begin_transaction();

    try {
        // Delete related entries from PRODUCTO_INGREDIENTE
        $stmt = $conection->prepare("DELETE FROM PRODUCTO_INGREDIENTE WHERE ID_Producto = ?");
        $stmt->bind_param("i", $id_producto);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting product-ingredient relations: " . $stmt->error);
        }
        $stmt->close();

        // Delete the product from Producto table
        $stmt = $conection->prepare("DELETE FROM Producto WHERE ID_Producto = ?");
        $stmt->bind_param("i", $id_producto);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting product: " . $stmt->error);
        }
        $stmt->close();

        // Commit transaction
        $conection->commit();
        $success = "Beverage deleted successfully.";
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
    <title>Eliminar Bebida</title>
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
        <h3>Eliminar Bebida</h3>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="id_producto" class="form-label">Seleccione Bebida</label>
                <select class="form-control" id="id_producto" name="id_producto" required>
                    <option value="">Seleccione...</option>
                    <?php while ($row = $beverages->fetch_assoc()): ?>
                        <option value="<?php echo $row['ID_Producto']; ?>"><?php echo $row['Nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn w-100">Eliminar</button>
        </form>
        <button class="btn mt-3 w-100" onclick="location.href='beverages.php'">Volver a Bebidas</button>
    </div>
</body>
</html>
