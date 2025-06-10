<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO complaints (title, description, status, resident_id) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("sss", $title, $description, $status);
    $stmt->execute();
}

header("Location: admin_dashboard.php");
exit();
?>