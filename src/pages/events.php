<?php
require_once '../includes/header.php';
require_once '../config/database.php';
require_once '../classes/EventManager.php';
require_once '../classes/VolunteerManager.php';

$database = new Database();
$db = $database->getConnection();

$eventManager = new EventManager($db);
$volunteerManager = new VolunteerManager($db);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create_event') {
        $event_name = $_POST['event_name'] ?? '';
        $date = $_POST['event_date'] ?? '';
        $location = $_POST['location'] ?? '';

        if ($eventManager->createEvent($event_name, $date, $location)) {
            $message = 'Event created.';
        } else {
            $message = 'Failed to create event.';
        }
    }

    if ($action === 'update_event') {
        $event_id = (int)($_POST['event_id'] ?? 0);
        $event_name = $_POST['event_name'] ?? '';
        $date = $_POST['event_date'] ?? '';
        $location = $_POST['location'] ?? '';

        if ($event_id && $eventManager->updateEvent($event_id, $event_name, $date, $location)) {
            $message = 'Event updated.';
        } else {
            $message = 'Failed to update event.';
        }
    }

    if ($action === 'delete_event') {
        $event_id = (int)($_POST['event_id'] ?? 0);
        if ($event_id && $eventManager->deleteEvent($event_id)) {
            $message = 'Event deleted.';
        } else {
            $message = 'Failed to delete event.';
        }
    }

    if ($action === 'add_task') {
        $event_id = (int)($_POST['event_id'] ?? 0);
        $task_description = $_POST['task_description'] ?? '';

        if ($event_id && $task_description && $eventManager->addTaskToEvent($event_id, $task_description)) {
            $message = 'Task added.';
        } else {
            $message = 'Failed to add task.';
        }
    }

    if ($action === 'update_task') {
        $task_id = (int)($_POST['task_id'] ?? 0);
        $task_description = $_POST['task_description'] ?? '';

        if ($task_id && $task_description && $eventManager->updateTask($task_id, $task_description)) {
            $message = 'Task updated.';
        } else {
            $message = 'Failed to update task.';
        }
    }

    if ($action === 'delete_task') {
        $task_id = (int)($_POST['task_id'] ?? 0);

        if ($task_id && $eventManager->deleteTask($task_id)) {
            $message = 'Task deleted.';
        } else {
            $message = 'Failed to delete task.';
        }
    }

    if ($action === 'record_participation') {
        $volunteer_id = (int)($_POST['volunteer_id'] ?? 0);
        $task_id = (int)($_POST['task_id'] ?? 0);
        $hours_worked = (float)($_POST['hours_worked'] ?? 0);

        if ($volunteer_id && $task_id && $eventManager->recordParticipation($volunteer_id, $task_id, $hours_worked)) {
            $message = 'Participation recorded.';
        } else {
            $message = 'Failed to record participation.';
        }
    }
}

$events = $eventManager->getAllEvents();
$tasksByEvent = [];
foreach ($events as $event) {
    $tasksByEvent[$event['id']] = $eventManager->getTasksByEvent($event['id']);
}
$volunteers = $volunteerManager->getVolunteers();
?>

<h1 class="h5">Events</h1>

<div class="card" style="margin-top:8px;">
    <div class="h5">Create Event</div>
    <?php if ($message): ?><div class="muted"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <form method="POST" action="events.php" style="margin-top:8px;">
        <input type="hidden" name="action" value="create_event">
        <div class="form-grid cols-3">
            <div class="form-group">
                <label class="small">Event name</label>
                <input name="event_name" class="input" required>
            </div>
            <div class="form-group">
                <label class="small">Date</label>
                <input name="event_date" type="date" class="input" required>
            </div>
            <div class="form-group">
                <label class="small">Location</label>
                <input name="location" class="input">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn" type="submit">Create New Event<i data-lucide="calendar-plus" width="18" height="18"></i></button>
        </div>
    </form>
</div>

<div class="card" style="margin-top:12px;">
    <div class="block-header">
        <div class="h5">Upcoming Events</div>
    </div>
    <?php if (empty($events)): ?>
        <div class="muted">No upcoming events.</div>
    <?php else: ?>
        <?php foreach($events as $event): ?>
            <div style="margin-bottom:12px;border-bottom: 1px dashed #ffffff34">
                <div class="block-header">
                    <div>
                        <div class="h5"><?php echo htmlspecialchars($event['event_name']); ?></div>
                        <div class="muted small"><?php echo htmlspecialchars($event['event_date'] . ' - ' . $event['location']); ?></div>
                        <a class="small muted" href="javascript:void(0)" data-open-modal="eventModal-<?php echo (int)$event['id']; ?>" style="text-decoration:none;">Edit event details</a>
                    </div>
                    <div class="row" style="align-items:center;">
                        <button class="btn" type="button" data-open-modal="participationModal-<?php echo (int)$event['id']; ?>">Record Participation <i data-lucide="clock-plus" width="18" height="18"></i></button>
                        <button class="btn ghost" type="button" data-open-modal="tasksModal-<?php echo (int)$event['id']; ?>">Edit Task <i data-lucide="pen" width="18" height="18"></i></button>
                        <button class="btn ghost" type="button" data-open-modal="eventModal-<?php echo (int)$event['id']; ?>">Edit Event Details <i data-lucide="settings" width="18" height="18"></i></button>
                    </div>
                </div>

                <div class="small" style="margin-top:8px;">Tasks</div>
                <?php if (empty($tasksByEvent[$event['id']])): ?>
                    <div class="muted small">No tasks yet.</div>
                <?php else: ?>
                    <?php foreach ($tasksByEvent[$event['id']] as $task): ?>
                        <div class="small muted">• <?php echo htmlspecialchars($task['task_description']); ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php foreach ($events as $event): ?>
    <div class="modal" id="participationModal-<?php echo (int)$event['id']; ?>" aria-hidden="true">
        <div class="modal-backdrop" data-close-modal></div>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="participationTitle-<?php echo (int)$event['id']; ?>">
            <div class="modal-header">
                <h2 class="h5" id="participationTitle-<?php echo (int)$event['id']; ?>">Record Participation</h2>
            </div>
            <?php if (empty($volunteers) || empty($tasksByEvent[$event['id']])): ?>
                <div class="muted">Add volunteers and tasks before recording participation.</div>
                <div class="modal-actions">
                    <button class="btn ghost" type="button" data-close-modal>Close<i data-lucide="x" width="18" height="18"></i></button>
                </div>
            <?php else: ?>
                <form method="POST" action="events.php" style="margin-top:8px;">
                    <input type="hidden" name="action" value="record_participation">
                    <div class="form-group">
                        <label class="small">Volunteer</label>
                        <select name="volunteer_id" class="input" required>
                            <?php foreach ($volunteers as $v): ?>
                                <option value="<?php echo (int)$v['id']; ?>">
                                    <?php echo htmlspecialchars($v['first_name'] . ' ' . $v['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small">Task</label>
                        <select name="task_id" class="input" required>
                            <?php foreach ($tasksByEvent[$event['id']] as $t): ?>
                                <option value="<?php echo (int)$t['id']; ?>">
                                    <?php echo htmlspecialchars($t['task_description']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small">Hours worked</label>
                        <input name="hours_worked" type="number" step="0.25" min="0" class="input" required>
                    </div>
                    <div class="modal-actions">
                        <button class="btn" type="submit">Record<i data-lucide="clock-plus" width="18" height="18"></i></button>
                        <button class="btn ghost" type="button" data-close-modal>Cancel<i data-lucide="x" width="18" height="18"></i></button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal" id="eventModal-<?php echo (int)$event['id']; ?>" aria-hidden="true">
        <div class="modal-backdrop" data-close-modal></div>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="eventTitle-<?php echo (int)$event['id']; ?>">
            <div class="modal-header">
                <h2 class="h5" id="eventTitle-<?php echo (int)$event['id']; ?>">Edit Event</h2>
            </div>

            <form method="POST" action="events.php" id="editEventForm-<?php echo (int)$event['id']; ?>">
                <input type="hidden" name="action" value="update_event">
                <input type="hidden" name="event_id" value="<?php echo (int)$event['id']; ?>">
                <div class="form-group">
                    <label class="small">Event name</label>
                    <input name="event_name" class="input" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="small">Date</label>
                    <input name="event_date" type="date" class="input" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="small">Location</label>
                    <input name="location" class="input" value="<?php echo htmlspecialchars($event['location']); ?>">
                </div>
            </form>

            <form method="POST" action="events.php" id="deleteEventForm-<?php echo (int)$event['id']; ?>" style="margin-top:12px;" onsubmit="return confirm('Delete this event?');">
                <input type="hidden" name="action" value="delete_event">
                <input type="hidden" name="event_id" value="<?php echo (int)$event['id']; ?>">
            </form>

            <div class="modal-actions" style="margin-top:12px;">
                <button class="btn danger" type="submit" form="deleteEventForm-<?php echo (int)$event['id']; ?>">Delete<i data-lucide="trash" width="18" height="18"></i></button>
                <button class="btn" type="submit" form="editEventForm-<?php echo (int)$event['id']; ?>">Save<i data-lucide="save" width="18" height="18"></i></button>
                <button class="btn ghost" type="button" data-close-modal>Cancel<i data-lucide="x" width="18" height="18"></i></button>
            </div>
        </div>
    </div>

    <div class="modal" id="tasksModal-<?php echo (int)$event['id']; ?>" aria-hidden="true">
        <div class="modal-backdrop" data-close-modal></div>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="tasksTitle-<?php echo (int)$event['id']; ?>">
            <div class="modal-header">
                <h2 class="h5" id="tasksTitle-<?php echo (int)$event['id']; ?>">Manage Tasks</h2>
            </div>

            <div>
                <div class="h5">Add Task</div>
                <form method="POST" action="events.php" style="margin-top:8px;">
                    <input type="hidden" name="action" value="add_task">
                    <input type="hidden" name="event_id" value="<?php echo (int)$event['id']; ?>">
                    <div class="inline">
                        <div class="form-group">
                            <label class="small">Task description</label>
                            <input name="task_description" class="input" placeholder="e.g. Distribute supplies" required>
                        </div>
                        <button class="btn" type="submit"><i data-lucide="plus" width="18" height="18"></i></button>
                    </div>
                </form>
            </div>

            <div style="margin-top:16px;">
                <div class="h5">Edit Tasks</div>
                <?php if (empty($tasksByEvent[$event['id']])): ?>
                    <div class="muted small" style="margin-top:8px;">No tasks yet.</div>
                <?php else: ?>
                    <?php foreach ($tasksByEvent[$event['id']] as $task): ?>
                        <form method="POST" action="events.php" style="display:flex; gap:8px; align-items:center; margin-top:8px;">
                            <input type="hidden" name="task_id" value="<?php echo (int)$task['id']; ?>">
                            <div class="form-group" style="flex:1; margin-bottom:0;">
                                <input name="task_description" class="input" value="<?php echo htmlspecialchars($task['task_description']); ?>" required>
                            </div>
                            <button class="btn" type="submit" name="action" value="update_task"><i data-lucide="save" width="18" height="18"></i></button>
                            <button class="btn danger" type="submit" name="action" value="delete_task" onclick="return confirm('Delete this task?');"><i data-lucide="trash" width="18" height="18"></i></button>
                        </form>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="modal-actions" style="margin-top:12px;">
                <button class="btn ghost" type="button" data-close-modal>Close<i data-lucide="x" width="18" height="18"></i></button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
(function() {
    const openButtons = document.querySelectorAll('[data-open-modal]');
    const closeButtons = document.querySelectorAll('[data-close-modal]');

    const openModal = (modal) => {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    };

    const closeModal = (modal) => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    };

    openButtons.forEach((btn) => {
        const targetId = btn.getAttribute('data-open-modal');
        btn.addEventListener('click', () => {
            const modal = document.getElementById(targetId);
            if (modal) openModal(modal);
        });
    });

    closeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal');
            if (modal) closeModal(modal);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('.modal.is-open').forEach((modal) => {
            closeModal(modal);
        });
    });
})();
</script>

<?php require_once '../includes/footer.php'; ?>