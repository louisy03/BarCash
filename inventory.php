<?php
session_start();

// Redirigir si no está logueado
if (!isset($_SESSION['id_administrador'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Modo noche */
        body {
            background-color: rgba(77, 76, 72, 255);
            color: white;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Contenedor de opciones */
        .options-container {
            background-color: rgba(58, 59, 58, 1);
            border-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem;
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }

        /* Sección de cada opción */
        .inventory-option {
            flex: 1;
            text-align: center;
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
        }

        .inventory-option:hover {
            transform: scale(1.05);
        }

        .inventory-option img {
            width: 200px;
            height: auto;
        }

        /* Ajuste para la imagen de bebidas */
        .inventory-option-beverages img {
            margin-bottom: 2rem; 
        }

        .inventory-option-ingredients img {
            width: 300px;
            margin-bottom: 3.1rem; 
        }

        /* Ajuste para el botón de ingredientes */
        .inventory-option-ingredients a {
            margin-top: auto;
        }

        .inventory-option a {
            display: inline-block;
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: white;
            font-weight: bold;
            border: 2px solid white;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
            margin-top: auto;
        }

        .inventory-option a:hover {
            background-color: white;
            color: black;
        }

        /* Separador vertical centrado */
        .separator {
            width: 2px;
            height: calc(100% - 4rem);
            background-color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Contenedor de opciones -->
        <div class="options-container">
            <!-- Opción de Bebidas -->
            <div class="inventory-option inventory-option-beverages">
                <img src="assets/images/mojito.png" alt="Bebidas">
                <a href="beverages.php">Bebidas</a>
            </div>

            <!-- Separador vertical -->
            <div class="separator"></div>

            <!-- Opción de Ingredientes -->
            <div class="inventory-option inventory-option-ingredients">
                <img src="assets/images/ingredientes.png" alt="Ingredientes">
                <a href="ingredients.php">Ingredientes</a>
            </div>
        </div>
    </div>

    <!-- JS de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
