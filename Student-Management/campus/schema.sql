CREATE DATABASE IF NOT EXISTS campus CHARACTER SET utf8mb4;
USE campus;
DROP TABLE IF EXISTS students, courses, users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);
CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(12) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL
);
CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  roll_no VARCHAR(20) NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL,
  phone VARCHAR(20),
  course_id INT NOT NULL,
  year TINYINT NOT NULL DEFAULT 1,
  status ENUM('active','graduated','suspended') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id)
);
-- Login: admin / admin123
INSERT INTO users (username, password_hash) VALUES ('admin', '$6$ZURAy.Fm3esxUW2o$RqcuJJjI.Z6ClSrAF81xhSjFl4jdbBgT2h1DuPeswUqjoyrh75XuIB5T6s3tfQ4lt3DrYgv4OvqkvnNz/i.Eh/');
INSERT INTO courses (code, name) VALUES
('DSD','Diploma in Software Development'),('DNW','Diploma in Networking'),('DGD','Diploma in Graphic Design'),('DAC','Diploma in Accounting');
INSERT INTO students (roll_no,full_name,email,phone,course_id,year,status) VALUES
('DSD-001','Ayesha Khan','ayesha.khan@example.com','0300-1111111',1,2,'active'),
('DSD-002','Bilal Ahmed','bilal.ahmed@example.com','0301-2222222',1,1,'active'),
('DSD-003','Hira Siddiqui','hira.s@example.com','0302-3333333',1,3,'graduated'),
('DNW-001','Usman Ali','usman.ali@example.com','0303-4444444',2,2,'active'),
('DNW-002','Sana Malik','sana.malik@example.com','0304-5555555',2,1,'active'),
('DGD-001','Zainab Raza','zainab.r@example.com','0305-6666666',3,2,'active'),
('DGD-002','Hamza Sheikh','hamza.s@example.com','0306-7777777',3,1,'suspended'),
('DAC-001','Fatima Noor','fatima.noor@example.com','0307-8888888',4,3,'graduated'),
('DAC-002','Omar Farooq','omar.f@example.com','0308-9999999',4,1,'active'),
('DSD-004','Maryam Iqbal','maryam.i@example.com','0309-1010101',1,1,'active');
