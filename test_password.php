<?php
$hash = '$2y$10$AnBZBRuzkudzE/uskxEyWe1WmVorOpMaXk1D7pEIrA6...'; // full hash here
$password = 'wecare123'; // try guessing what the original password was

if (password_verify($password, $hash)) {
    echo "✅ Password matches!";
} else {
    echo "❌ Password does NOT match.";
}
?>
