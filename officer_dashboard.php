<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'officer') {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
require_once 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Officer Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="dashboard-container">
    <h2>Welcome Officer, <?php echo htmlspecialchars($user['first_name']); ?></h2>
    <a href="logout.php">Logout</a>

    <h3>Your Assigned Complaints</h3>
    <table>
        <tr><th>ID</th><th>Title</th><th>Description</th><th>Status</th><th>Action</th></tr>
        <?php
        $stmt = $conn->prepare("SELECT * FROM complaints WHERE assigned_officer_id = ?");
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
            <td>
                <form action="update_complaint.php" method="post">
                    <input type="hidden" name="complaint_id" value="<?= $row['id'] ?>">
                    <select name="new_status">
                        <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
                        <option value="in_progress" <?= $row['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                        <option value="resolved" <?= $row['status']=='resolved'?'selected':'' ?>>Resolved</option>
                    </select>
                    <button type="submit">Update Status</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>