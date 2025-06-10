<?php
$error = $_GET['error'] ?? '';
$errorMsg = '';

switch ($error) {
    case 'user_not_found':
        $errorMsg = 'User not found.';
        break;
    case 'wrong_password':
        $errorMsg = 'Incorrect password.';
        break;
    case 'unauthorized':
        $errorMsg = 'Unauthorized access.';
        break;
    case 'invalid':
        $errorMsg = 'Invalid login attempt.';
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>WeCare - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($errorMsg): ?>
            <div class="error-message"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <form action="dashboard.php" method="post">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>