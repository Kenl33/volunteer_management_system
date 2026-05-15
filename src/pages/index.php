<?php
require_once '../includes/header.php';
require_once '../config/database.php';
require_once '../classes/ReportGenerator.php';

$database = new Database();
$db = $database->getConnection();
$report = new ReportGenerator($db);

$summary = $report->getSummaryStats();
$participation = $report->getVolunteerParticipationReport();
?>

<h1 class="h5">Dashboard</h1>
<div class="stats-grid" style="margin-top:12px;">
	<div class="card">
		<div class="h5">Total Volunteers</div>
		<div class="muted"><?php echo $summary['total_volunteers'] ?? 0; ?></div>
	</div>
	<div class="card">
		<div class="h5">Total Events</div>
		<div class="muted"><?php echo $summary['total_events'] ?? 0; ?></div>
	</div>
	<div class="card">
		<div class="h5">Total Hours</div>
		<div class="muted"><?php echo $summary['total_hours'] ?? 0; ?></div>
	</div>
</div>

<h2 class="h5" style="margin-top:20px;">Top Contributors</h2>
<div class="card" style="margin-top:8px;">
	<table class="table">
		<thead>
			<tr><th>Volunteer</th><th>Tasks</th><th>Hours</th></tr>
		</thead>
		<tbody>
		<?php if (!empty($participation)): foreach($participation as $row): ?>
			<tr>
				<td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
				<td><?php echo $row['tasks_completed'] ?? 0; ?></td>
				<td><?php echo $row['total_hours'] ?? 0; ?></td>
			</tr>
		<?php endforeach; else: ?>
			<tr><td colspan="3" class="center muted">No data available</td></tr>
		<?php endif; ?>
		</tbody>
	</table>
</div>

<?php require_once '../includes/footer.php'; ?>
