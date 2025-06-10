<?php
session_start();
require_once 'config.php';

function logAudit($conn, $email, $type) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO audit_log (email, violation_type, ip_address, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $email, $type, $ip);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?");
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            logAudit($conn, $email, "Login Success");

            switch ($user['role']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    exit();
                case 'officer':
                    header("Location: officer_dashboard.php");
                    exit();
                case 'resident':
                    header("Location: resident_dashboard.php");
                    exit();
                default:
                    logAudit($conn, $email, "Unauthorized Role");
                    header("Location: index.php?error=unauthorized");
                    exit();
            }
        } else {
            logAudit($conn, $email, "Wrong Password");
            header("Location: index.php?error=wrong_password");
            exit();
        }
    } else {
        logAudit($conn, $email, "Unknown Email");
        header("Location: index.php?error=user_not_found");
        exit();
    }
} else {
    echo "⚠️ No POST data received.";
    exit();
}
