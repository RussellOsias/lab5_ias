<?php
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'officer'])) {
    header("Location: index.php");
    exit();
}

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = $_POST['complaint_id'];
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $complaint_id);
    $stmt->execute();
}

header("Location: dashboard.php");
exit();
?>