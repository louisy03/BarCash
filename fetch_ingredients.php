<?php
include 'db/connection.php';

if (isset($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']);

    $stmt = $conection->prepare("
        SELECT i.ID_Ingrediente, i.Nombre, pi.Cantidad
        FROM PRODUCTO_INGREDIENTE pi
        INNER JOIN Ingrediente i ON pi.ID_Ingrediente = i.ID_Ingrediente
        WHERE pi.ID_Producto = ?
    ");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();

    $ingredients = [];
    while ($row = $result->fetch_assoc()) {
        $ingredients[] = $row;
    }

    echo json_encode($ingredients);
}
