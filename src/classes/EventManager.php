<?php

class EventManager {
    private $db;
    private $table = "events";

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    // Fetch all upcoming events
    public function getAllEvents() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY event_date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch a single event by id
    public function getEventById($event_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $event_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Create a new event
    public function createEvent($event_name, $date, $location) {
        $query = "INSERT INTO " . $this->table . " 
                  (event_name, event_date, location) 
                  VALUES (:event_name, :date, :location)";
        
        $stmt = $this->db->prepare($query);

        // Clean data
        $event_name = htmlspecialchars(strip_tags($event_name));

        $stmt->bindParam(':event_name', $event_name);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':location', $location);

        return $stmt->execute();
    }

    // Update an event
    public function updateEvent($event_id, $event_name, $date, $location) {
        $query = "UPDATE " . $this->table . " SET event_name = :event_name, event_date = :date, location = :location WHERE id = :id";
        $stmt = $this->db->prepare($query);

        $event_name = htmlspecialchars(strip_tags($event_name));

        $stmt->bindParam(':event_name', $event_name);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':id', $event_id);

        return $stmt->execute();
    }

    // Delete an event
    public function deleteEvent($event_id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $event_id);
        return $stmt->execute();
    }

    // Add a task to an event
    public function addTaskToEvent($event_id, $task_description) {
        $query = "INSERT INTO tasks (event_id, task_description) VALUES (:event_id, :task_description)";
        $stmt = $this->db->prepare($query);
        $task_description = htmlspecialchars(strip_tags($task_description));
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':task_description', $task_description);
        return $stmt->execute();
    }

    // Update a task
    public function updateTask($task_id, $task_description) {
        $query = "UPDATE tasks SET task_description = :task_description WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $task_description = htmlspecialchars(strip_tags($task_description));
        $stmt->bindParam(':task_description', $task_description);
        $stmt->bindParam(':id', $task_id);
        return $stmt->execute();
    }

    // Delete a task
    public function deleteTask($task_id) {
        $query = "DELETE FROM tasks WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $task_id);
        return $stmt->execute();
    }

    // Fetch tasks for a single event
    public function getTasksByEvent($event_id) {
        $query = "SELECT id, task_description FROM tasks WHERE event_id = :event_id ORDER BY id ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch all tasks with event names
    public function getAllTasks() {
        $query = "SELECT t.id, t.task_description, e.event_name FROM tasks t JOIN events e ON t.event_id = e.id ORDER BY e.event_date ASC, t.id ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Record participation hours for a task
    public function recordParticipation($volunteer_id, $task_id, $hours_worked) {
        $query = "INSERT INTO participation (volunteer_id, task_id, hours_worked) VALUES (:volunteer_id, :task_id, :hours_worked)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':volunteer_id', $volunteer_id);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':hours_worked', $hours_worked);
        return $stmt->execute();
    }

    // Volunteer applies to a task (event application)
    public function applyToTask($volunteer_id, $task_id) {
        $checkQuery = "SELECT id FROM participation WHERE volunteer_id = :volunteer_id AND task_id = :task_id LIMIT 1";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':volunteer_id', $volunteer_id);
        $checkStmt->bindParam(':task_id', $task_id);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            return false;
        }

        $query = "INSERT INTO participation (volunteer_id, task_id, status) VALUES (:volunteer_id, :task_id, 'applied')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':volunteer_id', $volunteer_id);
        $stmt->bindParam(':task_id', $task_id);
        return $stmt->execute();
    }

    // See volunteers who applied to an event
    public function getApplicationsByEvent($event_id) {
        $query = "SELECT 
                    p.id AS participation_id,
                    p.status,
                    p.hours_worked,
                    v.id AS volunteer_id,
                    v.first_name,
                    v.last_name,
                    v.email,
                    v.phone,
                    u.username,
                    t.id AS task_id,
                    t.task_description,
                    e.id AS event_id,
                    e.event_name,
                    e.event_date
                  FROM participation p
                  JOIN tasks t ON p.task_id = t.id
                  JOIN events e ON t.event_id = e.id
                  JOIN volunteers v ON p.volunteer_id = v.id
                  LEFT JOIN users u ON v.user_id = u.id
                  WHERE e.id = :event_id
                  ORDER BY t.id ASC, v.last_name ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}