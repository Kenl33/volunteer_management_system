<?php
require_once '../includes/header.php';
require_once '../config/database.php';
require_once '../classes/EventManager.php';
require_once '../classes/VolunteerManager.php';

if (($_SESSION['role'] ?? '') !== 'volunteer') {
    header('Location: index.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$eventManager = new EventManager($db);
$volunteerManager = new VolunteerManager($db);

$volunteer = $volunteerManager->ensureVolunteerByUser($_SESSION['user_id'] ?? 0, $_SESSION['username'] ?? '');
$mainEvent = null;
$mainTasks = [];

if ($volunteer) {
    $query = "SELECT e.id, e.event_name, e.event_date, e.location, p.status
              FROM participation p
              JOIN tasks t ON p.task_id = t.id
              JOIN events e ON t.event_id = e.id
              WHERE p.volunteer_id = :volunteer_id
              ORDER BY e.event_date DESC
              LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':volunteer_id', $volunteer['id']);
    $stmt->execute();
    $mainEvent = $stmt->fetch();

    if (!empty($mainEvent)) {
        $taskQuery = "SELECT t.task_description, p.status
                      FROM participation p
                      JOIN tasks t ON p.task_id = t.id
                      WHERE p.volunteer_id = :volunteer_id AND t.event_id = :event_id
                      ORDER BY t.id ASC";
        $taskStmt = $db->prepare($taskQuery);
        $taskStmt->bindParam(':volunteer_id', $volunteer['id']);
        $taskStmt->bindParam(':event_id', $mainEvent['id']);
        $taskStmt->execute();
        $mainTasks = $taskStmt->fetchAll();
    }
}
?>

<h1 class="h5">My Main Event</h1>

<?php if (!$volunteer): ?>
    <div class="card" style="margin-top:8px;">
        <div class="muted">Volunteer profile not found.</div>
    </div>
<?php elseif (empty($mainEvent)): ?>
    <div class="card" style="margin-top:8px;">
        <div class="h5">No event yet</div>
        <div class="muted" style="margin-top:6px;">Browse events and apply to get started.</div>
        <div style="margin-top:12px;">
            <a class="btn" href="volunteer_events.php">View Events</a>
        </div>
    </div>
<?php else: ?>
    <div class="card" style="margin-top:8px;">
        <div class="h5"><?php echo htmlspecialchars($mainEvent['event_name']); ?></div>
        <div class="muted small"><?php echo htmlspecialchars($mainEvent['event_date'] . ' • ' . $mainEvent['location']); ?></div>
        <div class="small" style="margin-top:12px;">Your tasks</div>
        <?php if (empty($mainTasks)): ?>
            <div class="muted small" style="margin-top:6px;">No tasks yet.</div>
        <?php else: ?>
            <?php foreach ($mainTasks as $task): ?>
                <div class="small muted">• <?php echo htmlspecialchars($task['task_description']); ?> (<?php echo htmlspecialchars($task['status']); ?>)</div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
