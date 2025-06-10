<?php
$conn = new mysqli("localhost", "resident_user", "ResidentPass123!", "wecare_clean");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, title, AES_DECRYPT(description_encrypted, 'SecureKey123') AS description FROM complaints";
$result = $conn->query($sql);

$complaints = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $complaints[] = $row;
    }
}

echo json_encode($complaints);
$conn->close();
?>