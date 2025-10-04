<?php
$servername = "localhost";
$username = "school_user";
$password = "password";
$dbname = "school_management";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($dbname);

// SQL to create tables
$sql_admins = "CREATE TABLE IF NOT EXISTS admins (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$sql_teachers = "CREATE TABLE IF NOT EXISTS teachers (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    subject_taught VARCHAR(50),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$sql_students = "CREATE TABLE IF NOT EXISTS students (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    class VARCHAR(20),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$sql_parents = "CREATE TABLE IF NOT EXISTS parents (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    student_id INT(6) UNSIGNED,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql_admins) === TRUE) {
    // echo "Table 'admins' created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

if ($conn->query($sql_teachers) === TRUE) {
    // echo "Table 'teachers' created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

if ($conn->query($sql_students) === TRUE) {
    // echo "Table 'students' created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

if ($conn->query($sql_parents) === TRUE) {
    // echo "Table 'parents' created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

?>