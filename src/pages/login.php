<?php
require_once '../config/database.php';
require_once '../classes/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!empty($_GET['action']) && $_GET['action'] === 'logout') {
    $auth->logout();
    header('Location: login.php');
    exit();
}

if ($auth->isLoggedIn()) {
    $redirect = ($_SESSION['role'] ?? '') === 'volunteer' ? 'volunteer_home.php' : 'index.php';
    header("Location: {$redirect}");
    exit();
}

$activeTab = $_GET['tab'] ?? 'login';
if (!in_array($activeTab, ['login', 'register'], true)) {
    $activeTab = 'login';
}

$login_error = '';
$register_error = '';
$register_success = '';
$login_username = '';
$register_data = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => ''
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'register') {
        $activeTab = 'register';
        $register_data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? '')
        ];

        $password = $_POST['reg_password'] ?? '';

        if (
            $register_data['first_name'] === '' ||
            $register_data['last_name'] === '' ||
            $register_data['email'] === '' ||
            $password === ''
        ) {
            $register_error = 'Please fill out all required fields.';
        } else {
            $result = $auth->registerVolunteer([
                'username' => $register_data['email'],
                'password' => $password,
                'first_name' => $register_data['first_name'],
                'last_name' => $register_data['last_name'],
                'email' => $register_data['email'],
                'phone' => $register_data['phone']
            ]);

            if ($result !== false) {
                $register_success = 'Account created. Please sign in.';
                $register_data = [
                    'first_name' => '',
                    'last_name' => '',
                    'email' => '',
                    'phone' => ''
                ];
                $activeTab = 'login';
            } else {
                $register_error = 'Registration failed. Please try again.';
            }
        }
    } else {
        $activeTab = 'login';
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $login_username = $username;

        if ($auth->login($username, $password)) {
            $redirect = ($_SESSION['role'] ?? '') === 'volunteer' ? 'volunteer_home.php' : 'index.php';
            header("Location: {$redirect}");
            exit();
        } else {
            $login_error = "Invalid username or password.";
        }
    }
}
?>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Volunteer Management</title>
    <link rel="stylesheet" href="../styles/theme.css">
	<link rel="stylesheet" href="../styles/login.css">
</head>
<body>
    <div class="login">
        <div class="top">
            <a style='font-weight: 800; cursor: pointer; color: var(--color-accent)' href="landing_page.php">Back to Landing Page</a>
        </div>

        <div class="tabs">
            <a class="tab <?php echo $activeTab === 'login' ? 'active' : ''; ?>" href="login.php?tab=login">Login</a>
            <a class="tab <?php echo $activeTab === 'register' ? 'active' : ''; ?>" href="login.php?tab=register">Register</a>
        </div>
        <?php if ($activeTab === 'login'): ?>
            <form method="POST" action="login.php?tab=login" class="auth-form">
                <div class="welcome">
                    <h1>Welcome Back</h1>
                    <h5>Sign in to continue to your dashboard</h5>
                </div>
                <input type="hidden" name="action" value="login">

                <?php if ($register_success): ?>
                    <div class="success"><?php echo htmlspecialchars($register_success); ?></div>
                <?php endif; ?>

                <?php if ($login_error): ?>
                    <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username" class="small">Email</label>
                    <input type="text" id="username" name="username" class="input" value="<?php echo htmlspecialchars($login_username); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password" class="small">Password</label>
                    <input type="password" id="password" name="password" class="input" required>
                </div>
                <button type="submit" class="btn">LOGIN</button>
            </form>
        <?php else: ?>
            <form method="POST" action="login.php?tab=register" class="auth-form">
                <input type="hidden" name="action" value="register">
                <div class="welcome">
                    <h1>Create a Volunteer Account</h1>
                    <h5>Join your community and start making an impact!</h5>
                </div>
                <?php if ($register_error): ?>
                    <div class="error"><?php echo htmlspecialchars($register_error); ?></div>
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="small">First name</label>
                        <input type="text" id="first_name" name="first_name" class="input" value="<?php echo htmlspecialchars($register_data['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="small">Last name</label>
                        <input type="text" id="last_name" name="last_name" class="input" value="<?php echo htmlspecialchars($register_data['last_name']); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="small">Email</label>
                    <input type="email" id="email" name="email" class="input" value="<?php echo htmlspecialchars($register_data['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone" class="small">Phone (optional)</label>
                    <input type="text" id="phone" name="phone" class="input" value="<?php echo htmlspecialchars($register_data['phone']); ?>">
                </div>
                <div class="form-group">
                    <label for="reg_password" class="small">Password</label>
                    <input type="password" id="reg_password" name="reg_password" class="input" required>
                </div>
                <button type="submit" class="btn">REGISTER</button>
            </form>
        <?php endif; ?>

        <div class="bottom">
            <h5>All Rights Reserved</h5>
            <h5>VolunteerHub 2026</h5>
        </div>
    </div>
</body>