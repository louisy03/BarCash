<?php
// Generate a hashed password
$hashed_password = password_hash("123", PASSWORD_DEFAULT);
echo $hashed_password;
?>
