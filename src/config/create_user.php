<?php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$new_user = 'kensali123';
$new_pass = 'kensali123';

$hashed_password = password_hash($new_pass, PASSWORD_BCRYPT);

try {
    $query = "INSERT INTO users (username, password) VALUES (:user, :pass)";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':user', $new_user);
    $stmt->bindParam(':pass', $hashed_password);

    if($stmt->execute()) {
        echo "User created successfully!";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}