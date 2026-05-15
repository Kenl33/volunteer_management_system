<?php

class VolunteerManager {
    private $db;
    private $table = "volunteers";

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    /**
     * Fetch all volunteers.
     */
    public function getVolunteers() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY last_name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch volunteer by user id.
     */
    public function getVolunteerByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Ensure a volunteer profile exists for a user.
     */
    public function ensureVolunteerByUser($user_id, $username) {
        $existing = $this->getVolunteerByUserId($user_id);
        if ($existing) {
            return $existing;
        }

        if (!$user_id || !$username) {
            return null;
        }

        $firstName = $username;
        $lastName = $username;
        $email = $username;

        $query = "INSERT INTO " . $this->table . " (user_id, first_name, last_name, email, phone) VALUES (:user_id, :first_name, :last_name, :email, NULL)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            return $this->getVolunteerByUserId($user_id);
        }

        return null;
    }

    /**
     * Add a new volunteer to the system.
     */
    public function registerVolunteer($data) {
        $query = "INSERT INTO " . $this->table . " 
              (first_name, last_name, email, phone) 
              VALUES (:fname, :lname, :email, :phone)";
        
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':fname', $data['first_name']);
        $stmt->bindParam(':lname', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        // email is unique in schema; let DB enforce uniqueness

        return $stmt->execute();
    }

    /**
     * Update an existing volunteer's full profile.
     */
    public function updateVolunteer($id, $data) {
        $query = "UPDATE " . $this->table . " SET first_name = :fname, last_name = :lname, email = :email, phone = :phone WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fname', $data['first_name']);
        $stmt->bindParam(':lname', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Delete a volunteer by id.
     */
    public function deleteVolunteer($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Search volunteers by name, email, or phone.
     */
    public function searchVolunteers($term) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE first_name LIKE :term 
                  OR last_name LIKE :term 
                  OR email LIKE :term
                  OR phone LIKE :term";
        
        $searchTerm = "%$term%";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':term', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}