<!DOCTYPE html>
<html>
<head>
    <title>Audit Analysis - Admin</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];

$topViolators = $conn->query("SELECT email, COUNT(*) AS count FROM audit_log WHERE violation_type != 'Login Success' GROUP BY email ORDER BY count DESC LIMIT 5");
$commonViolations = $conn->query("SELECT violation_type, COUNT(*) AS count FROM audit_log WHERE violation_type != 'Login Success' GROUP BY violation_type ORDER BY count DESC");
$violationsPerDay = $conn->query("SELECT DATE(timestamp) as day, COUNT(*) as count FROM audit_log WHERE timestamp >= CURDATE() - INTERVAL 7 DAY GROUP BY day ORDER BY day ASC");
$violationsPerHour = $conn->query("SELECT HOUR(timestamp) as hour, COUNT(*) as count FROM audit_log WHERE timestamp >= CURDATE() - INTERVAL 1 DAY GROUP BY hour ORDER BY hour ASC");
?>
<div class="dashboard-container">
    <h2>Audit Log Analysis</h2>
    <a href="audit.php">&larr; Back to Audit Logs</a>

    <h3>Top 5 Violating Emails</h3>
    <table>
        <tr><th>Email</th><th>Count</th></tr>
        <?php
        $violatorEmails = [];
        $violatorCounts = [];
        while ($row = $topViolators->fetch_assoc()):
            $violatorEmails[] = $row['email'];
            $violatorCounts[] = $row['count'];
        ?>
        <tr>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['count'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <canvas id="emailChart" height="100"></canvas>

    <h3>Most Common Violation Types</h3>
    <table>
        <tr><th>Violation Type</th><th>Count</th></tr>
        <?php
        $violationLabels = [];
        $violationCounts = [];
        $commonViolations->data_seek(0);
        while ($row = $commonViolations->fetch_assoc()):
            $violationLabels[] = $row['violation_type'];
            $violationCounts[] = $row['count'];
        ?>
        <tr>
            <td><?= htmlspecialchars($row['violation_type']) ?></td>
            <td><?= $row['count'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <canvas id="violationChart" height="100"></canvas>

    <h3>Violation Trends (Last 7 Days)</h3>
    <table>
        <tr><th>Date</th><th>Violations</th></tr>
        <?php
        $dayLabels = [];
        $dayCounts = [];
        while ($row = $violationsPerDay->fetch_assoc()):
            $dayLabels[] = $row['day'];
            $dayCounts[] = $row['count'];
        ?>
        <tr>
            <td><?= (new DateTime($row['day']))->format("F j, Y") ?></td>
            <td><?= $row['count'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <canvas id="trendChart" height="100"></canvas>

    <h3>Violations by Hour (Past 24 Hours)</h3>
    <canvas id="hourChart" height="100"></canvas>
</div>

<script>
    const emailChart = new Chart(document.getElementById('emailChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($violatorEmails) ?>,
            datasets: [{
                label: 'Violations by Email',
                data: <?= json_encode($violatorCounts) ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        }
    });

    const violationChart = new Chart(document.getElementById('violationChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($violationLabels) ?>,
            datasets: [{
                data: <?= json_encode($violationCounts) ?>,
                backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#2ecc71']
            }]
        }
    });

    const trendChart = new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($dayLabels) ?>,
            datasets: [{
                label: 'Violations per Day',
                data: <?= json_encode($dayCounts) ?>,
                borderColor: '#007bff',
                fill: false,
                tension: 0.2
            }]
        }
    });

    const hourChart = new Chart(document.getElementById('hourChart'), {
        type: 'bar',
        data: {
            labels: [...Array(24).keys()].map(h => h + ":00"),
            datasets: [{
                label: 'Violations per Hour',
                data: <?php
                    $hourData = array_fill(0, 24, 0);
                    while ($row = $violationsPerHour->fetch_assoc()) {
                        $hourData[(int)$row['hour']] = $row['count'];
                    }
                    echo json_encode($hourData);
                ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        }
    });
</script>
</body>
</html>
