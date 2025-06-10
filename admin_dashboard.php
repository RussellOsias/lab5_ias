<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
require_once 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="dashboard-container">
    <h2>Welcome Admin, <?php echo htmlspecialchars($user['first_name']); ?></h2>
    <a href="logout.php">Logout</a>

    <h3>All Complaints</h3>
    <table>
        <tr><th>ID</th><th>Title</th><th>Description</th><th>Status</th><th>Actions</th></tr>
        <?php
        $result = $conn->query("SELECT * FROM complaints");
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <form action="update_complaint.php" method="post" style="display:inline;">
                    <input type="hidden" name="complaint_id" value="<?= $row['id'] ?>">
                    <select name="new_status">
                        <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
                        <option value="in_progress" <?= $row['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                        <option value="resolved" <?= $row['status']=='resolved'?'selected':'' ?>>Resolved</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
                <form action="delete_complaint.php" method="post" style="display:inline;">
                    <input type="hidden" name="complaint_id" value="<?= $row['id'] ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Add New Complaint</h3>
    <form action="add_complaint.php" method="post">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <select name="status">
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="resolved">Resolved</option>
        </select>
        <button type="submit">Add Complaint</button>
    </form>
</div>
</body>
</html>