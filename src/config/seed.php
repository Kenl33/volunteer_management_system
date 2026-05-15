<?php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

try {
    echo "Starting seeding...<br>";

    // 1. Seed Users (Admin)
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $db->exec("INSERT INTO users (username, password) VALUES ('admin', '$password')");
    echo "Users seeded.<br>";

    // 2. Seed Volunteers
    $volunteers = [
        ['John', 'Doe', 'john@example.com', '09123456789'],
        ['Jane', 'Smith', 'jane@example.com', '09987654321'],
        ['Ahlan', 'Sencio', 'ahlan@example.com', '09555444333']
    ];

    $stmt = $db->prepare("INSERT INTO volunteers (first_name, last_name, email, phone) VALUES (?, ?, ?, ?)");
    foreach ($volunteers as $v) {
        $stmt->execute($v);
    }
    echo "Volunteers seeded.<br>";

    // 3. Seed Events
    $events = [
        ['Community Clean-up', '2026-06-01', 'Zamboanga City Park'],
        ['Tech Workshop', '2026-06-15', 'WMSU IT Lab']
    ];

    $stmt = $db->prepare("INSERT INTO events (event_name, event_date, location) VALUES (?, ?, ?)");
    foreach ($events as $e) {
        $stmt->execute($e);
    }
    echo "Events seeded.<br>";

    // 4. Seed Tasks
    $tasks = [
        [1, 'Collect plastic waste'],
        [1, 'Segregate recyclables'],
        [2, 'Set up laptops'],
        [2, 'Assist attendees']
    ];

    $stmt = $db->prepare("INSERT INTO tasks (event_id, task_description) VALUES (?, ?)");
    foreach ($tasks as $t) {
        $stmt->execute($t);
    }
    echo "Tasks seeded.<br>";

    // 5. Seed Participation
    $participation = [
        [1, 1, 4.5],
        [2, 2, 5.0],
        [3, 3, 3.0]
    ];

    $stmt = $db->prepare("INSERT INTO participation (volunteer_id, task_id, hours_worked) VALUES (?, ?, ?)");
    foreach ($participation as $p) {
        $stmt->execute($p);
    }
    echo "Participation seeded.<br>";

    echo "<strong>Seeding completed successfully!</strong>";

} catch (PDOException $e) {
    // If you run this twice, it might fail due to UNIQUE constraints
    echo "Seeding failed: " . $e->getMessage();
}