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
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4f8;
        }

        .dashboard-container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 220px;
            background: #1e3a8a;
            color: white;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar h3 {
            margin-top: 0;
            font-size: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 10px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            padding: 10px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        h2 {
            margin-top: 0;
            color: #1e3a8a;
        }

        h3 {
            margin-top: 40px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #1e3a8a;
            color: white;
            text-align: left;
        }

        form {
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="text"], textarea, select {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            background-color: #1e3a8a;
            color: white;
            padding: 10px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #3b5fc4;
        }

        .action-form {
            display: inline;
        }

        select {
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Admin Menu</h3>
        <a href="admin_dashboard.php">📊 Dashboard</a>
        <a href="audit.php">🔍 Audit Logs</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Welcome Admin, <?= htmlspecialchars($user['first_name']); ?> 👋</h2>

        <h3>📌 All Complaints</h3>
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
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <form action="update_complaint.php" method="post" class="action-form">
                        <input type="hidden" name="complaint_id" value="<?= $row['id'] ?>">
                        <select name="new_status">
                            <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="in_progress" <?= $row['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                            <option value="resolved" <?= $row['status']=='resolved'?'selected':'' ?>>Resolved</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                    <form action="delete_complaint.php" method="post" class="action-form">
                        <input type="hidden" name="complaint_id" value="<?= $row['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h3>➕ Add New Complaint</h3>
        <form action="add_complaint.php" method="post">
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="description" placeholder="Description" rows="3" required></textarea>
            <select name="status" required>
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
