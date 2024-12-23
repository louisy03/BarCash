<?php
session_start();
include 'db/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $contrasena = $_POST['contrasena'];

    // Query to fetch the user's hashed password
    $stmt = $conection->prepare("SELECT ID_Administrador, Contrasena FROM administrador WHERE Nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_administrador, $db_password);
        $stmt->fetch();

        // Verify the hashed password
        if (password_verify($contrasena, $db_password)) {
            $_SESSION['id_administrador'] = $id_administrador;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
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
            background: rgba(0, 0, 0, 0.85);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 600px; 
        }

        .logo {
            width: 200px;
            height: auto;
            margin: 0 auto;
        }

        .login-card h3,
        .login-card .form-label {
            color: white;
        }

        .login-card input[type="text"],
        .login-card input[type="password"] {
            background-color: rgba(132, 133, 132, 255);
            border-color: rgba(132, 133, 132, 255);
            color: white;
        }

        .login-card button[type="submit"] {
            background-color: white;
            color: black;
            border-color: white;
        }

        .login-form {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .login-fields {
            flex: 1;
            margin-right: 20px; 
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-form">
            <div class="login-fields">
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
            <!-- Logo -->
            <img src="assets/images/logo.png" alt="Logo" class="logo">
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
