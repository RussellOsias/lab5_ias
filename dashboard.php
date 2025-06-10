<?php
session_start();
require_once 'config.php';

echo "<h3>Debug Info:</h3>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    echo "<strong>Email Entered:</strong> $email <br>";
    echo "<strong>Password Entered:</strong> $password <br><br>";

    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        echo "<pre>";
        print_r($user);
        echo "</pre>";

        echo "Stored Hash: " . $user['password'] . "<br>";

        if (password_verify($password, $user['password'])) {
            echo "✅ Password Matched!<br>";
            $_SESSION['user'] = $user;

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
            echo "❌ Password Mismatch.<br>";
            header("Location: index.php?error=wrong_password");
            exit();
        }
    } else {
        echo "❌ User Not Found.<br>";
        header("Location: index.php?error=user_not_found");
        exit();
    }

    echo "🔄 Redirecting back to login...<br>";
    header("Location: index.php?error=invalid");
    exit();
} else {
    echo "⚠️ No POST data received.";
    exit();
}
?>