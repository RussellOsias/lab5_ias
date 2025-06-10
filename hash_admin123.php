<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "UPDATE users SET password = '$hash';";
?>
