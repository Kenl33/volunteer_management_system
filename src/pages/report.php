<?php
require_once '../includes/header.php';
require_once '../config/database.php';
require_once '../classes/ReportGenerator.php';

$database = new Database();
$db = $database->getConnection();
$report = new ReportGenerator($db);

$summary = $report->getSummaryStats();
$participation = $report->getVolunteerParticipationReport();
$events = $report->getEventEngagementReport();
$taskCounts = $report->getVolunteersWithTaskCounts();
?>

<h1 class="h5">Reports</h1>
<div class="stats-grid" style="margin-top:12px;">
    <div class="card">
        <div class="h5">Total Hours</div>
        <div class="muted"><?php echo $summary['total_hours'] ?? 0; ?></div>
    </div>
    <div class="card">
        <div class="h5">Active Volunteers</div>
        <div class="muted"><?php echo $summary['total_volunteers'] ?? 0; ?></div>
    </div>
    <div class="card">
        <div class="h5">Total Events</div>
        <div class="muted"><?php echo $summary['total_events'] ?? 0; ?></div>
    </div>
</div>

<h2 class="h5" style="margin-top:18px;">Volunteer Participation</h2>
<div class="card" style="margin-top:8px;">
    <table class="table">
        <thead><tr><th>Volunteer</th><th>Tasks</th><th>Hours</th></tr></thead>
        <tbody>
            <?php if (!empty($participation)): foreach($participation as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                    <td><?php echo $row['tasks_completed'] ?? 0; ?></td>
                    <td><?php echo $row['total_hours'] ?? 0; ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="3" class="center muted">No participation data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<h2 class="h5" style="margin-top:18px;">Event Engagement</h2>
<div class="card" style="margin-top:8px;">
    <table class="table">
        <thead><tr><th>Event</th><th>Date</th><th>Tasks</th><th>Total Hours</th></tr></thead>
        <tbody>
            <?php if (!empty($events)): foreach($events as $e): ?>
                <tr>
                    <td><?php echo htmlspecialchars($e['event_name']); ?></td>
                    <td><?php echo htmlspecialchars($e['event_date']); ?></td>
                    <td><?php echo $e['task_count'] ?? 0; ?></td>
                    <td><?php echo $e['total_hours'] ?? 0; ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4" class="center muted">No event engagement data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<h2 class="h5" style="margin-top:18px;">Task Distribution</h2>
<div class="card" style="margin-top:8px;">
    <table class="table">
        <thead><tr><th>Volunteer</th><th>Total Tasks</th></tr></thead>
        <tbody>
            <?php if (!empty($taskCounts)): foreach($taskCounts as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                    <td><?php echo $row['total_tasks']; ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="2" class="center muted">No task data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
