<?php
$servername = "sql312.infinityfree.com";
$username = "if0_40086614";
$password = "3z61mIXR0Ws";
$dbname = "if0_40086614_test";

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
    profile_image VARCHAR(255),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$sql_grades = "CREATE TABLE IF NOT EXISTS grades (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grade_name VARCHAR(50) NOT NULL UNIQUE
)";

$sql_sections = "CREATE TABLE IF NOT EXISTS sections (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(50) NOT NULL,
    grade_id INT(6) UNSIGNED,
    FOREIGN KEY (grade_id) REFERENCES grades(id) ON DELETE CASCADE
)";

$sql_subjects = "CREATE TABLE IF NOT EXISTS subjects (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL UNIQUE
)";

$sql_teacher_assignments = "CREATE TABLE IF NOT EXISTS teacher_assignments (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT(6) UNSIGNED,
    section_id INT(6) UNSIGNED,
    subject_id INT(6) UNSIGNED,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
)";

$sql_students = "CREATE TABLE IF NOT EXISTS students (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    phone VARCHAR(20),
    section_id INT(6) UNSIGNED,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
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

if ($conn->query($sql_grades) === TRUE) {
    $grades = ["Grade 1", "Grade 2", "Grade 3", "Grade 4", "Grade 5", "Grade 6"];
    $stmt = $conn->prepare("INSERT INTO grades (grade_name) VALUES (?) ON DUPLICATE KEY UPDATE grade_name=grade_name");
    foreach ($grades as $grade) {
        $stmt->bind_param("s", $grade);
        $stmt->execute();
    }
} else {
    echo "Error creating table: " . $conn->error;
}

if ($conn->query($sql_sections) === TRUE) {
    // Check if sections are empty
    $sql_check_sections = "SELECT id FROM sections LIMIT 1";
    $result_check_sections = $conn->query($sql_check_sections);
    if ($result_check_sections->num_rows == 0) {
        $sql_get_grades = "SELECT id FROM grades";
        $grades_result = $conn->query($sql_get_grades);
        if ($grades_result->num_rows > 0) {
            $stmt_section = $conn->prepare("INSERT INTO sections (section_name, grade_id) VALUES (?, ?)");
            $sections = ['Section A', 'Section B', 'Section C'];
            while ($grade_row = $grades_result->fetch_assoc()) {
                foreach ($sections as $section) {
                    $stmt_section->bind_param("si", $section, $grade_row['id']);
                    $stmt_section->execute();
                }
            }
        }
    }
} else {
    echo "Error creating table: " . $conn->error;
}

if ($conn->query($sql_subjects) === TRUE) {
    $subjects = ["Mathematics", "Science", "English", "Filipino", "Araling Panlipunan", "Edukasyon sa Pagpapakatao", "Music, Arts, Physical Education, and Health (MAPEH)"];
    $stmt = $conn->prepare("INSERT INTO subjects (subject_name) VALUES (?) ON DUPLICATE KEY UPDATE subject_name=subject_name");
    foreach ($subjects as $subject) {
        $stmt->bind_param("s", $subject);
        $stmt->execute();
    }
} else {
    echo "Error creating table: " . $conn->error;
}

if ($conn->query($sql_teacher_assignments) === TRUE) {
    // echo "Table 'teacher_assignments' created successfully or already exists<br>";
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

$sql_attendance = "CREATE TABLE IF NOT EXISTS attendance (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT(6) UNSIGNED NOT NULL,
    subject_id INT(6) UNSIGNED NOT NULL,
    teacher_id INT(6) UNSIGNED NOT NULL,
    class_date DATE NOT NULL,
    status ENUM('present', 'absent') NOT NULL,
    UNIQUE KEY unique_attendance (student_id, subject_id, class_date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
)";

if ($conn->query($sql_attendance) === TRUE) {
    // echo "Table 'attendance' created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

?>