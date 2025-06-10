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
    <title>Admin Dashboard - WeCare</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dashboard-container {
            display: flex;
        }
        .sidebar {
            width: 200px;
            padding: 20px;
            background-color: #f2f2f2;
            height: 100vh;
        }
        .sidebar a {
            display: block;
            padding: 10px 0;
            text-decoration: none;
            color: #333;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Admin Menu</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="audit.php">🔍 Audit Logs</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Welcome Admin, <?= htmlspecialchars($user['first_name']); ?></h2>

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
</div>

</body>
</html>
