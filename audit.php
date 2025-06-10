<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];

// Filter out non-violations like Login Success
$logs = $conn->query("SELECT * FROM audit_log WHERE violation_type != 'Login Success' ORDER BY timestamp DESC");

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=audit_logs.csv");

    $output = fopen("php://output", "w");
    fputcsv($output, ['Email', 'Violation Type', 'Timestamp']);

    $csv = $conn->query("SELECT email, violation_type, timestamp FROM audit_log WHERE violation_type != 'Login Success' ORDER BY timestamp DESC");
    while ($row = $csv->fetch_assoc()) {
        fputcsv($output, [
            $row['email'],
            $row['violation_type'],
            (new DateTime($row['timestamp']))->format("F j, Y g:i A")
        ]);
    }
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs - Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="dashboard-container">
    <h2>Welcome, <?= htmlspecialchars($user['first_name']) ?> (Admin)</h2>
    <a href="admin_dashboard.php">← Back to Dashboard</a> |
    <a href="audit.php?export=csv">Download CSV</a>

    <h3>Audit Log Entries</h3>
    <table>
        <tr>
            <th>Email</th>
            <th>Violation Type</th>
            <th>Timestamp</th>
        </tr>
        <?php while ($log = $logs->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($log['email']) ?></td>
                <td><?= htmlspecialchars($log['violation_type']) ?></td>
                <td><?= (new DateTime($log['timestamp']))->format("F j, Y g:i A") ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
