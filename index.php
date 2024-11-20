<!-- File: index.php -->
<?php
session_start();
include 'db/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $contrasena = $_POST['contrasena'];

    // Query database for user
    $stmt = $conection->prepare("SELECT ID_Administrador, Contrasena FROM administrador WHERE Nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_administrador, $db_password);
        $stmt->fetch();

        // Verify password
        if ($contrasena === $db_password) { // Replace with password_verify() if hashing is implemented
            $_SESSION['id_administrador'] = $id_administrador;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcash Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Fullscreen background with blur */
        body {
            background: url('assets/images/fondo_cucei.png') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Card styling */
        .login-card {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        /* Logo styling */
        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Logo -->
        <img src="assets/images/logo.png" alt="Logo" class="logo">
        <h3 class="mb-4">Inicio de sesión</h3>

        <!-- Login Form -->
        <form method="POST" action="">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Username -->
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="contrasena" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
