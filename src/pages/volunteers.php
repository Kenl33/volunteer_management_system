<?php
require_once '../includes/header.php';
require_once '../config/database.php';
require_once '../classes/VolunteerManager.php';

$database = new Database();
$db = $database->getConnection();
$manager = new VolunteerManager($db);

$message = '';
// Handle create / update / delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create') {
        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
        ];

        if ($manager->registerVolunteer($data)) {
            $message = 'Volunteer added.';
        } else {
            $message = 'Failed to add volunteer.';
        }

    } elseif ($action === 'update' && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
        ];

        if ($manager->updateVolunteer($id, $data)) {
            $message = 'Volunteer updated.';
        } else {
            $message = 'Failed to update volunteer.';
        }

    } elseif ($action === 'delete' && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        if ($manager->deleteVolunteer($id)) {
            $message = 'Volunteer deleted.';
        } else {
            $message = 'Failed to delete volunteer.';
        }
    }
}

$searchTerm = $_GET['search'] ?? '';
if (!empty($searchTerm)) {
    $list = $manager->searchVolunteers($searchTerm);
} else {
    $list = $manager->getVolunteers();
}
?>

<h1 class="h5">Volunteers</h1>

<div class="card" style="margin-top:8px;">
    <div class="h5">Add Volunteer</div>
    <?php if ($message): ?><div class="muted"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <form method="POST" action="volunteers.php" style="margin-top:8px;">
        <input type="hidden" name="action" value="create">
        <div class="form-grid cols-4">
            <div class="form-group">
                <label class="small">First name</label>
                <input name="first_name" class="input" required>
            </div>
            <div class="form-group">
                <label class="small">Last name</label>
                <input name="last_name" class="input" required>
            </div>
            <div class="form-group">
                <label class="small">Email</label>
                <input name="email" type="email" class="input">
            </div>
            <div class="form-group">
                <label class="small">Phone</label>
                <input name="phone" class="input">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn" type="submit">Add New Volunteer<i data-lucide="user-plus" width="18" height="18"></i></button>
        </div>
    </form>
</div>

<div class="card" style="margin-top:12px;">
    <form method="GET" action="volunteers.php" class="search">
        <input type="text" name="search" class="input" placeholder="Search by name, email, or phone" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button class="btn" type="submit">Search<i data-lucide="search" width="18" height="18"></i></button>
    </form>

    <table class="table" style="margin-top:12px;">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Phone</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (!empty($list)): foreach ($list as $v): ?>
            <tr>
                <td><?php echo htmlspecialchars($v['first_name'] . ' ' . $v['last_name']); ?></td>
                <td><?php echo htmlspecialchars($v['email']); ?></td>
                <td><?php echo htmlspecialchars($v['phone']); ?></td>
                <td class="center">
                    <button
                        class="btn ghost"
                        type="button"
                        data-edit
                        data-id="<?php echo (int)$v['id']; ?>"
                        data-first-name="<?php echo htmlspecialchars($v['first_name'], ENT_QUOTES); ?>"
                        data-last-name="<?php echo htmlspecialchars($v['last_name'], ENT_QUOTES); ?>"
                        data-email="<?php echo htmlspecialchars($v['email'], ENT_QUOTES); ?>"
                        data-phone="<?php echo htmlspecialchars($v['phone'], ENT_QUOTES); ?>"
                    >Edit <i data-lucide="pen" width="18" height="18"></i></button>
                    <form method="POST" action="volunteers.php" style="display:inline-block;margin-left:8px;" onsubmit="return confirm('Delete this volunteer?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo (int)$v['id']; ?>">
                        <button class="btn danger" type="submit">Delete<i data-lucide="trash" width="18" height="18"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="4" class="center muted">No volunteers found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal" id="editModal" aria-hidden="true">
    <div class="modal-backdrop" data-close></div>
    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
        <div class="modal-header">
            <h2 class="h5" id="editModalTitle">Edit Volunteer</h2>
        </div>
        <form method="POST" action="volunteers.php" id="editForm">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="">
            <div class="form-group">
                <label class="small">First name</label>
                <input name="first_name" class="input" required>
            </div>
            <div class="form-group">
                <label class="small">Last name</label>
                <input name="last_name" class="input" required>
            </div>
            <div class="form-group">
                <label class="small">Email</label>
                <input name="email" type="email" class="input">
            </div>
            <div class="form-group">
                <label class="small">Phone</label>
                <input name="phone" class="input">
            </div>
            <div class="modal-actions">
                <button class="btn" type="submit">Save <i data-lucide="save" width="18" height="18"></i></button>
                <button class="btn ghost" type="button" data-close>Cancel<i data-lucide="x" width="18" height="18"></i></button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('editModal');
    if (!modal) return;

    const idInput = modal.querySelector('input[name="id"]');
    const firstInput = modal.querySelector('input[name="first_name"]');
    const lastInput = modal.querySelector('input[name="last_name"]');
    const emailInput = modal.querySelector('input[name="email"]');
    const phoneInput = modal.querySelector('input[name="phone"]');

    const openButtons = document.querySelectorAll('[data-edit]');
    const closeButtons = modal.querySelectorAll('[data-close]');

    const openModal = (data) => {
        idInput.value = data.id || '';
        firstInput.value = data.firstName || '';
        lastInput.value = data.lastName || '';
        emailInput.value = data.email || '';
        phoneInput.value = data.phone || '';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    };

    openButtons.forEach((btn) => {
        btn.addEventListener('click', () => openModal(btn.dataset));
    });

    closeButtons.forEach((btn) => {
        btn.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeModal();
    });
})();
</script>

<?php require_once '../includes/footer.php'; ?>