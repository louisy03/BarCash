<?php
session_start();
if (!isset($_SESSION['id_administrador'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcash Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Fullscreen background */
        body {
            background-color: rgba(77, 76, 72, 255);
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            color: white;
        }

        /* Logo styling */
        .logo {
            width: 1000px;
            height: auto;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 10%;
            opacity: 0.4;
            z-index: 1;
        }

        /* Buttons box styling */
        .buttons-box {
            background-color: rgba(58, 59, 58, 1);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-align: center;
            margin-top: calc(30% + 50px);
            z-index: 2;
        }

        /* Buttons styling */
        .buttons-box a {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 0 1rem;
            padding: 0.5rem;
            display: inline-block;
        }

        /* Button separator */
        .buttons-box a:not(:last-child)::after {
            content: "|";
            margin-left: 1rem;
            color: white;
        }

        /* Hover effect */
        .buttons-box a:hover {
            color: rgba(200, 200, 200, 1);
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <img src="assets/images/logo.png" alt="Logo" class="logo">

    <!-- Buttons Box -->
    <div class="buttons-box">
        <a href="inventory.php">Gestión de Inventario</a>
        <a href="orders.php">Registrar Pedidos</a>
        <a href="records.php">Registro de Ventas</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
