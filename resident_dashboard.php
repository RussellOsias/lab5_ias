<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resident') {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
require_once 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resident Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="dashboard-container">
    <h2>Welcome Resident, <?php echo htmlspecialchars($user['first_name']); ?></h2>
    <a href="logout.php">Logout</a>

    <h3>Your Complaints</h3>
    <table>
        <tr><th>ID</th><th>Title</th><th>Description</th><th>Status</th></tr>
        <?php
        $stmt = $conn->prepare("SELECT * FROM complaints WHERE resident_id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>