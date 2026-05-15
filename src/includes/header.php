<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
// If user is not logged in, redirect to login.php for any page other than login.php
$current = basename($_SERVER['PHP_SELF']);
if (empty($_SESSION['user_id']) && $current !== 'login.php') {
	header('Location: login.php');
	exit();
}

$role = $_SESSION['role'] ?? 'admin';
$navLinks = $role === 'volunteer'
	? [
		'volunteer_home.php' => 'My Event',
		'volunteer_events.php' => 'Events'
	]
	: [
		'index.php' => 'Dashboard',
		'volunteers.php' => 'Volunteers',
		'events.php' => 'Events',
		'report.php' => 'Reports'
	];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Volunteer Management</title>
	<link rel="stylesheet" href="../styles/theme.css">
	<link rel="stylesheet" href="../styles/shared.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
</head>
<body class="app-shell">
	<aside class="sidebar">
		<div class="sidebar-brand">VolunteerHub</div>
		<nav class="sidebar-nav">
			<?php foreach ($navLinks as $link => $label): ?>
				<a href="<?php echo htmlspecialchars($link); ?>"><?php echo htmlspecialchars($label); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php if (!empty($_SESSION['username'])): ?>
			<div class="sidebar-user">
				<div class="sidebar-user-card">
					<div class="sidebar-user-name">
						<i data-lucide="user" width="16" height="16"></i>
						<span>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
					</div>
					<span class="sidebar-user-role"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
				</div>
				<a class="btn ghost" href="login.php?action=logout">Logout<i data-lucide="log-out" width="16" height="16"></i></a>
			</div>
		<?php endif; ?>
	</aside>
	<div class="app-content">
		<main class="container">
