CREATE DATABASE IF NOT EXISTS isu_leave_system_modern CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE isu_leave_system_modern;

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
    role ENUM('admin','head','teaching','non_teaching') NOT NULL,
    department VARCHAR(150) NOT NULL,
    position VARCHAR(150) NULL,
    vacation_leave DECIMAL(10,1) NOT NULL DEFAULT 15.0,
    sick_leave DECIMAL(10,1) NOT NULL DEFAULT 15.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type ENUM('Vacation','Sick','Study','Others') NOT NULL,
    days DECIMAL(10,1) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NOT NULL,
    commutation VARCHAR(50) NULL,
    specific_details TEXT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Pending Admin Review',
    admin_action_by INT NULL,
    admin_action_at DATETIME NULL,
    admin_remark VARCHAR(255) NULL,
    head_action_by INT NULL,
    head_action_at DATETIME NULL,
    head_remark VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_leave_emp FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_leave_admin FOREIGN KEY (admin_action_by) REFERENCES users(id) ON DELETE SET NULL,
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

INSERT INTO users (employee_no, fullname, email, password, role, department, position, vacation_leave, sick_leave) VALUES
('ADM-001', 'HRMO Administrator', 'admin@isu.local', '$2y$10$4s8MBr05ZvcS1f7TtTrq7eN50W5P3R6W/6US9X8bVsgcuyo6edS7S', 'admin', 'HRMO', 'HRMO Officer', 15.0, 15.0),
('HEAD-001', 'Campus Head', 'head@isu.local', '$2y$10$4s8MBr05ZvcS1f7TtTrq7eN50W5P3R6W/6US9X8bVsgcuyo6edS7S', 'head', 'Administration', 'Campus Head', 15.0, 15.0),
('TCH-001', 'Teaching Personnel Demo', 'teaching@isu.local', '$2y$10$4s8MBr05ZvcS1f7TtTrq7eN50W5P3R6W/6US9X8bVsgcuyo6edS7S', 'teaching', 'College of Computing Studies', 'Instructor I', 12.0, 10.0),
('NT-001', 'Non-Teaching Personnel Demo', 'nonteaching@isu.local', '$2y$10$4s8MBr05ZvcS1f7TtTrq7eN50W5P3R6W/6US9X8bVsgcuyo6edS7S', 'non_teaching', 'Registrar', 'Administrative Aide', 12.0, 10.0);

INSERT INTO notifications (user_id, message) VALUES
(1, 'Admin account is ready.'),
(2, 'Head account is ready.'),
(3, 'Teaching personnel account is ready.'),
(4, 'Non-teaching personnel account is ready.');
