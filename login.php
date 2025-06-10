<?php
session_start();
require_once 'config.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

function logAudit($conn, $email, $type) {
    $stmt = $conn->prepare("INSERT INTO audit_log (email, violation_type, ip_address, timestamp) VALUES (?, ?, ?, NOW())");
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("sss", $email, $type, $ip);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Generate OTP and store user session
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expires'] = time() + 300; // expires in 5 minutes
            $_SESSION['user'] = $user;

            // Send OTP via PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'valoaccs1928@gmail.com';
                $mail->Password = 'zvqmkguzrnwurscd';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('valoaccs1928@gmail.com', 'WeCare OTP');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body = "Your OTP is: <b>$otp</b><br>This code expires in 5 minutes.";
                $mail->send();

                header("Location: verify.php");
                exit();
            } catch (Exception $e) {
                logAudit($conn, $email, "OTP Email Failed");
                header("Location: index.php?error=invalid");
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
}
?>
