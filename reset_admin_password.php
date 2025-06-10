<?php
$password = 'wecare123'; // new password you want
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Run this SQL in phpMyAdmin:<br><br>";
echo "UPDATE users SET password = '$hash' WHERE email = 'admin@gmail.com';";
?>
