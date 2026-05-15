DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS participation;
DROP TABLE IF EXISTS volunteers;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'volunteer') DEFAULT 'volunteer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(255) NOT NULL
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    task_description VARCHAR(255) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE participation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT NOT NULL,
    task_id INT NOT NULL,
    status ENUM('applied', 'approved', 'completed') DEFAULT 'applied',
    hours_worked DECIMAL(5,2) DEFAULT 0.00,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, role) VALUES 
('admin', 'admin123', 'admin'),
('juan_delacruz', 'password123', 'volunteer'),
('maria_clara', 'password123', 'volunteer'),
('jose_rizal', 'password123', 'volunteer');

INSERT INTO volunteers (user_id, first_name, last_name, email, phone) VALUES
(2, 'Juan', 'Dela Cruz', 'juan@example.com', '09123456789'),
(3, 'Maria', 'Clara', 'maria@example.com', '09987654321'),
(4, 'Jose', 'Rizal', 'jose@example.com', '09456123789');

INSERT INTO events (event_name, event_date, location) VALUES
('Coastal Cleanup', '2026-06-15', 'Boulevard Beach'),
('Tree Planting', '2026-07-20', 'Pasonanca Park');

INSERT INTO tasks (event_id, task_description) VALUES
(1, 'Collect plastic waste'),
(1, 'Segregate recyclables'),
(2, 'Dig holes for seedlings'),
(2, 'Distribute water to volunteers');

INSERT INTO participation (volunteer_id, task_id, status, hours_worked) VALUES
(1, 1, 'completed', 4.5),
(2, 2, 'completed', 5.0),
(3, 3, 'applied', 0.00),
(1, 4, 'approved', 0.00),
(2, 3, 'completed', 4.0);