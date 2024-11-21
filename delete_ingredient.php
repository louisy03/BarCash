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

    // Delete the ingredient from the database
    $stmt = $conection->prepare("DELETE FROM Ingrediente WHERE ID_Ingrediente = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Ingrediente eliminado exitosamente.";
    } else {
        $error = "Error al eliminar el ingrediente: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Ingrediente</title>
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
</head>
<body>
    <div class="form-box">
        <h3>Eliminar Ingrediente</h3>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="id_ingrediente" class="form-label">Seleccione Ingrediente</label>
                <select class="form-control" id="id_ingrediente" name="id_ingrediente" required>
                    <option value="">Seleccione...</option>
                    <?php while ($row = $ingredients->fetch_assoc()): ?>
                        <option value="<?php echo $row['ID_Ingrediente']; ?>"><?php echo $row['Nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn w-100">Eliminar</button>
        </form>
        <button class="btn mt-3 w-100" onclick="location.href='ingredients.php'">Volver a Ingredientes</button>
    </div>
</body>
</html>
