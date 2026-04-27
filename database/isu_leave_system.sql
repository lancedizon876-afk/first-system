CREATE DATABASE IF NOT EXISTS isu_leave_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE isu_leave_system;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS leave_requests;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_no VARCHAR(50) NULL,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('employee','hrmo','head') NOT NULL,
    department VARCHAR(150) NULL,
    vacation_leave DECIMAL(10,1) NOT NULL DEFAULT 15.0,
    sick_leave DECIMAL(10,1) NOT NULL DEFAULT 15.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type ENUM('Vacation','Sick') NOT NULL,
    days DECIMAL(10,1) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NOT NULL,
    attachment_note TEXT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Pending HRMO',
    hrmo_action_by INT NULL,
    hrmo_action_at DATETIME NULL,
    hrmo_remark VARCHAR(255) NULL,
    head_action_by INT NULL,
    head_action_at DATETIME NULL,
    head_remark VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_leave_employee FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_leave_hrmo FOREIGN KEY (hrmo_action_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_leave_head FOREIGN KEY (head_action_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO users (employee_no, fullname, email, password, role, department, vacation_leave, sick_leave) VALUES
('EMP-001', 'Juan Dela Cruz', 'employee@isu.local', '$2y$10$4s8MBr05ZvcS1f7TtTrq7eN50W5P3R6W/6US9X8bVsgcuyo6edS7S', 'employee', 'Registrar', 10.0, 8.0),
('HR-001', 'Maria Santos', 'hrmo@isu.local', '$2y$10$4s8MBr05ZvcS1f7TtTrq7eN50W5P3R6W/6US9X8bVsgcuyo6edS7S', 'hrmo', 'HRMO', 15.0, 15.0),
('HEAD-001', 'Dr. Head ISU', 'head@isu.local', '$2y$10$4s8MBr05ZvcS1f7TtTrq7eN50W5P3R6W/6US9X8bVsgcuyo6edS7S', 'head', 'Administration', 15.0, 15.0);

INSERT INTO notifications (user_id, message) VALUES
(1, 'Welcome to the ISU Leave Management System.'),
(2, 'HRMO account is ready for leave review.'),
(3, 'Head account is ready for final approval.');
