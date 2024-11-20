<?php
$conection = new mysqli("localhost", "root", "", "barcashdb");

// Set character set to UTF-8 for multilingual support
$conection->set_charset("utf8");

// Check connection
if ($conection->connect_error) {
    die("Connection failed: " . $conection->connect_error);
}
?>
