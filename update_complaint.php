<?php
session_start();
require_once 'config.php';

// Check if user is logged in and form is submitted
if (!isset($_SESSION['user']) || !isset($_POST['complaint_id'], $_POST['new_status'])) {
    header("Location: index.php");
    exit();
}

$complaint_id = intval($_POST['complaint_id']);
$new_status = $_POST['new_status'];

// Validate status input
$valid_statuses = ['pending', 'in_progress', 'resolved'];
if (!in_array($new_status, $valid_statuses)) {
    die("❌ Invalid status selected.");
}

// Prepare and execute the update
$stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $complaint_id);

if ($stmt->execute()) {
    // Redirect back depending on user role
    if ($_SESSION['user']['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else if ($_SESSION['user']['role'] === 'officer') {
        header("Location: officer_dashboard.php");
    } else {
        echo "✅ Status updated, but no redirect.";
    }
} else {
    echo "❌ Failed to update complaint status.";
}
?>
