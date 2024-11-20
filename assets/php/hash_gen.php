<?php
include 'db/connection.php';

// Datos del usuario
$nombre = "Juan";
$password = "123";

// Hashear la contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insertar el usuario en la base de datos
$stmt = $conection->prepare("INSERT INTO administrador (Nombre, Contrasena) VALUES (?, ?)");
$stmt->bind_param("ss", $nombre, $hashed_password);

if ($stmt->execute()) {
    echo "Usuario registrado exitosamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conection->close();
?>
