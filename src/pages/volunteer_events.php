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
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action']) && $volunteer) {
    if ($_POST['action'] === 'apply_task') {
        $taskId = (int)($_POST['task_id'] ?? 0);
        if ($taskId && $eventManager->applyToTask($volunteer['id'], $taskId)) {
            $message = 'Application submitted.';
        } else {
            $message = 'You already applied or the task is invalid.';
        }
    }
}

$events = $eventManager->getAllEvents();
$tasksByEvent = [];
foreach ($events as $event) {
    $tasksByEvent[$event['id']] = $eventManager->getTasksByEvent($event['id']);
}

$appliedTasks = [];
if ($volunteer) {
    $appliedQuery = "SELECT task_id FROM participation WHERE volunteer_id = :volunteer_id";
    $appliedStmt = $db->prepare($appliedQuery);
    $appliedStmt->bindParam(':volunteer_id', $volunteer['id']);
    $appliedStmt->execute();
    $appliedTasks = $appliedStmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<h1 class="h5">Events</h1>

<?php if ($message): ?>
    <div class="card" style="margin-top:8px;">
        <div class="muted"><?php echo htmlspecialchars($message); ?></div>
    </div>
<?php endif; ?>

<?php if (!$volunteer): ?>
    <div class="card" style="margin-top:8px;">
        <div class="muted">Volunteer profile not found.</div>
    </div>
<?php elseif (empty($events)): ?>
    <div class="card" style="margin-top:8px;">
        <div class="muted">No events available.</div>
    </div>
<?php else: ?>
    <?php foreach ($events as $event): ?>
        <div class="card" style="margin-top:12px;">
            <div class="h5"><?php echo htmlspecialchars($event['event_name']); ?></div>
            <div class="muted small"><?php echo htmlspecialchars($event['event_date'] . ' • ' . $event['location']); ?></div>

            <div class="small" style="margin-top:10px;">Tasks</div>
            <?php if (empty($tasksByEvent[$event['id']])): ?>
                <div class="muted small">No tasks yet.</div>
            <?php else: ?>
                <?php foreach ($tasksByEvent[$event['id']] as $task): ?>
                    <form method="POST" action="volunteer_events.php" style="margin-top:8px;">
                        <input type="hidden" name="action" value="apply_task">
                        <input type="hidden" name="task_id" value="<?php echo (int)$task['id']; ?>">
                        <div class="row" style="align-items:center;">
                            <div style="flex:1;">
                                <div class="small muted">• <?php echo htmlspecialchars($task['task_description']); ?></div>
                            </div>
                            <?php if (in_array($task['id'], $appliedTasks, true)): ?>
                                <span class="small muted">Applied</span>
                            <?php else: ?>
                                <button class="btn" type="submit">Apply</button>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
