CREATE DATABASE hospital_management_system_db;

USE hospital_management_system_db;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `failed_attempts` int(11) NOT NULL DEFAULT '0',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(50) DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;




CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    specialty VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE nurses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    id_number VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    address TEXT,
    contact VARCHAR(15)
);

CREATE TABLE beds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bed_number INT NOT NULL,
    status ENUM('occupied', 'vacant') NOT NULL
);

CREATE TABLE `medical_records` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `patient_id` int(11) NOT NULL,
    `doctor_id` int(11) NOT NULL,
    `nurse_id` int(11) NOT NULL,
    `record_date` date NOT NULL,
    `condition` varchar(255) NOT NULL,
    `remarks` text,
    `photo` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
);
