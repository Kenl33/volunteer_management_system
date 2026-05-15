<?php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$new_user = 'admin123';
$new_pass = 'admin123';

$hashed_password = password_hash($new_pass, PASSWORD_BCRYPT);

try {
    $query = "INSERT INTO users (username, password, role) VALUES (:user, :pass, 'admin')";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':user', $new_user);
    $stmt->bindParam(':pass', $hashed_password);

    if($stmt->execute()) {
        echo "User created successfully!";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}