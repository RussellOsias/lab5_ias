<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];

// Filter parameters
$search = $_GET['search'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

// Build WHERE clause
$where = "WHERE violation_type != 'Login Success'";
if ($search) {
    $safeSearch = $conn->real_escape_string($search);
    $where .= " AND (email LIKE '%$safeSearch%' OR violation_type LIKE '%$safeSearch%')";
}
if ($from && $to) {
    $where .= " AND DATE(timestamp) BETWEEN '$from' AND '$to'";
}

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=audit_logs.csv");

    $output = fopen("php://output", "w");
    fputcsv($output, ['Email', 'Violation Type', 'Timestamp']);

    $csv = $conn->query("SELECT email, violation_type, timestamp FROM audit_log $where ORDER BY timestamp DESC");
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

// Main query
$logs = $conn->query("SELECT * FROM audit_log $where ORDER BY timestamp DESC");

// Stats
$totalLogs = $conn->query("SELECT COUNT(*) AS count FROM audit_log WHERE violation_type != 'Login Success'")->fetch_assoc()['count'];
$invalidOTPs = $conn->query("SELECT COUNT(*) AS count FROM audit_log WHERE violation_type = 'Invalid OTP'")->fetch_assoc()['count'];
$wrongPasswords = $conn->query("SELECT COUNT(*) AS count FROM audit_log WHERE violation_type = 'Wrong Password'")->fetch_assoc()['count'];
$unknownEmails = $conn->query("SELECT COUNT(*) AS count FROM audit_log WHERE violation_type = 'Unknown Email'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs - Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .stats-box {
            display: flex;
            gap: 2rem;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat {
            background: #f4f4f4;
            padding: 1rem;
            border-radius: 10px;
            flex: 1;
            min-width: 150px;
            text-align: center;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .filter-form input[type="text"], .filter-form input[type="date"] {
            padding: 5px;
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <h2>Welcome, <?= htmlspecialchars($user['first_name']) ?> (Admin)</h2>
    <p>
        <a href="admin_dashboard.php">← Back to Dashboard</a> |
        <a href="analyze_audit.php">📊 Analyze Logs</a> |
        <a href="audit.php?export=csv<?= $search ? "&search=$search" : '' ?><?= $from ? "&from=$from&to=$to" : '' ?>">⬇ Download CSV</a>
    </p>

    <div class="stats-box">
        <div class="stat"><strong>Total Violations</strong><br><?= $totalLogs ?></div>
        <div class="stat"><strong>Invalid OTPs</strong><br><?= $invalidOTPs ?></div>
        <div class="stat"><strong>Wrong Passwords</strong><br><?= $wrongPasswords ?></div>
        <div class="stat"><strong>Unknown Emails</strong><br><?= $unknownEmails ?></div>
    </div>

    <form class="filter-form" method="GET">
        <input type="text" name="search" placeholder="Search email or violation..." value="<?= htmlspecialchars($search) ?>">
        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>">
        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>">
        <button type="submit">Apply Filter</button>
        <a href="audit.php">Reset</a>
    </form>

    <h3>Audit Log Entries</h3>
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Violation Type</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($logs->num_rows > 0): ?>
            <?php while ($log = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($log['email']) ?></td>
                    <td><?= htmlspecialchars($log['violation_type']) ?></td>
                    <td><?= (new DateTime($log['timestamp']))->format("F j, Y g:i A") ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No logs found for current filter.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
