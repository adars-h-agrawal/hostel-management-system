
CREATE DATABASE IF NOT EXISTS hostel_management;
USE hostel_management;

CREATE TABLE students (
  student_id INT AUTO_INCREMENT PRIMARY KEY,
  registration_number VARCHAR(50) UNIQUE,
  full_name VARCHAR(100),
  email VARCHAR(100),
  phone VARCHAR(20),
  password VARCHAR(255),
  room_number VARCHAR(10),
  block VARCHAR(10),
  room_type VARCHAR(20),
  address TEXT,
  dob DATE,
  blood_group VARCHAR(5),
  date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students (registration_number, full_name, email, phone, password, room_number, block, room_type, address, dob, blood_group)
VALUES 
('ST001', 'John Doe', 'john@example.com', '9876543210', 'student123', 'A101', 'A', 'Single AC', 'Hostel Block A', '2000-01-15', 'O+');

CREATE TABLE admins (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255),
  name VARCHAR(100),
  email VARCHAR(100)
);

INSERT INTO admins (username, password, name, email)
VALUES ('admin', 'admin123', 'Super Admin', 'admin@hostel.com');

CREATE TABLE complaints (
  complaint_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  complaint_type VARCHAR(50),
  description TEXT,
  status ENUM('Pending','In Progress','Resolved') DEFAULT 'Pending',
  resolution TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

CREATE TABLE attendance (
  attendance_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  date DATE,
  status ENUM('Present','Absent') DEFAULT 'Present',
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

CREATE TABLE fees (
  fee_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  semester VARCHAR(20),
  amount DECIMAL(10,2),
  status ENUM('Paid','Unpaid') DEFAULT 'Unpaid',
  payment_date DATE,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

CREATE TABLE notices (
  notice_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100),
  description TEXT,
  start_date DATE,
  end_date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE maintenance (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  request_type VARCHAR(50),
  description TEXT,
  image_path VARCHAR(255),
  status ENUM('Pending','In Progress','Resolved') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

CREATE TABLE guest_log (
  guest_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  guest_name VARCHAR(100),
  relationship VARCHAR(50),
  visit_date DATE,
  purpose TEXT,
  status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

CREATE TABLE mess_feedback (
  feedback_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  rating_quality INT,
  rating_hygiene INT,
  rating_service INT,
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

CREATE TABLE reminders (
  reminder_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  type VARCHAR(50),
  message TEXT,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE student_login (
  login_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  answer VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

INSERT INTO student_login (student_id, password_hash, answer)
VALUES (1, 'student123', 'Bruno');
