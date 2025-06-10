<?php
session_start();
require_once 'config.php';

$errorMsg = '';
$user = $_SESSION['user'] ?? null;
$otpExpires = $_SESSION['otp_expires'] ?? 0;
$correctOtp = strval($_SESSION['otp'] ?? ''); // Convert to string to avoid type mismatch

if (!$user) {
    header("Location: index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = trim($_POST['otp'] ?? '');

    if (time() > $otpExpires) {
        $errorMsg = "OTP has expired. Please login again.";
        session_destroy(); // Clear all session data
    } elseif ($enteredOtp === $correctOtp) {
        // OTP is correct — clear OTP data
        unset($_SESSION['otp'], $_SESSION['otp_expires']);

        // Redirect based on role
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
                header("Location: index.php?error=unauthorized");
                exit();
        }
    } else {
        // Invalid OTP — log the attempt
        $email = $user['email'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $conn->prepare("INSERT INTO audit_log (email, violation_type, ip_address, timestamp) VALUES (?, 'Invalid OTP', ?, NOW())");
        $stmt->bind_param("ss", $email, $ip);
        $stmt->execute();
        $errorMsg = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="login-container">
    <h2>Verify OTP</h2>
    <?php if ($errorMsg): ?>
        <div class="error-message"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>
</div>
</body>
</html>
