<?php
class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($username, $password) {
        $query = "SELECT id, username, role, password FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $isValid = password_verify($password, $row['password']) || hash_equals($row['password'], $password);
            if ($isValid) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                return true;
            }
        }
        return false;
    }

    public function registerVolunteer($data) {
        $required = ['username', 'password', 'first_name', 'last_name', 'email'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $phone = isset($data['phone']) && $data['phone'] !== '' ? $data['phone'] : null;

        try {
            $this->conn->beginTransaction();

            $userQuery = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'volunteer')";
            $userStmt = $this->conn->prepare($userQuery);
            $userStmt->bindParam(':username', $data['username']);
            $userStmt->bindParam(':password', $hashed);
            $userStmt->execute();

            $userId = $this->conn->lastInsertId();

            $volQuery = "INSERT INTO volunteers (user_id, first_name, last_name, email, phone) VALUES (:user_id, :first_name, :last_name, :email, :phone)";
            $volStmt = $this->conn->prepare($volQuery);
            $volStmt->bindParam(':user_id', $userId);
            $volStmt->bindParam(':first_name', $data['first_name']);
            $volStmt->bindParam(':last_name', $data['last_name']);
            $volStmt->bindParam(':email', $data['email']);
            $volStmt->bindValue(':phone', $phone, $phone === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $volStmt->execute();

            $this->conn->commit();
            return (int)$userId;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        $_SESSION = array();
        session_destroy();
        return true;
    }
}
?>