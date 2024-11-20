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
            background-color: rgba(77, 76, 72, 255); /* Night mode color */
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
            width: 1000px; /* 5x larger */
            height: auto;
            position: absolute;
            left: 50%;
            transform: translateX(-50%); /* Center horizontally */
            bottom: 10%; /* Much lower position */
            opacity: 0.7; /* 30% transparency */
            z-index: 1; /* Move logo to back */
        }

        /* Buttons box styling */
        .buttons-box {
            background-color: rgba(58, 59, 58, 1); /* Box color */
            color: white; /* White text */
            padding: 1rem 2rem;
            border-radius: 10px;
            text-align: center;
            margin-top: calc(30% + 50px); /* Adjust margin to ensure buttons box is below the logo */
            z-index: 2; /* Ensure buttons are in front */
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
            color: white; /* Separator color */
        }

        /* Hover effect */
        .buttons-box a:hover {
            color: rgba(200, 200, 200, 1); /* Slightly lighter text on hover */
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
