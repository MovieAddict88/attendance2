<?php
$servername = "127.0.0.1";
$username = "school_user";
$password = "password";
$dbname = "school_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Drop existing tables
$conn->query("SET foreign_key_checks = 0");
$tables = ['admins', 'teachers', 'grades', 'sections', 'subjects', 'teacher_assignments', 'students', 'parents'];
foreach ($tables as $table) {
    $conn->query("DROP TABLE IF EXISTS $table");
    echo "Dropped table $table\n";
}
$conn->query("SET foreign_key_checks = 1");


// SQL to create tables (copied from includes/database.php)
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

$conn->query($sql_admins);
$conn->query($sql_teachers);
$conn->query($sql_grades);
$conn->query($sql_sections);
$conn->query($sql_subjects);
$conn->query($sql_teacher_assignments);
$conn->query($sql_students);
$conn->query($sql_parents);

echo "Tables created successfully.\n";

// Seed data
$grades = ["Grade 1", "Grade 2", "Grade 3", "Grade 4", "Grade 5", "Grade 6"];
$stmt = $conn->prepare("INSERT INTO grades (grade_name) VALUES (?)");
foreach ($grades as $grade) {
    $stmt->bind_param("s", $grade);
    $stmt->execute();
}

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

$subjects = ["Mathematics", "Science", "English", "Filipino", "Araling Panlipunan", "Edukasyon sa Pagpapakatao", "Music, Arts, Physical Education, and Health (MAPEH)"];
$stmt = $conn->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
foreach ($subjects as $subject) {
    $stmt->bind_param("s", $subject);
    $stmt->execute();
}

// Seed a teacher
$full_name = 'John Doe';
$email = 'john.doe@example.com';
$password = password_hash('password123', PASSWORD_DEFAULT);
$stmt_teacher = $conn->prepare("INSERT INTO teachers (full_name, email, password) VALUES (?, ?, ?)");
$stmt_teacher->bind_param("sss", $full_name, $email, $password);
$stmt_teacher->execute();
$teacher_id = $stmt_teacher->insert_id;

// Assign this teacher to the first section and subject
$sql_first_section = "SELECT id FROM sections LIMIT 1";
$result_first_section = $conn->query($sql_first_section);
$section_id = $result_first_section->fetch_assoc()['id'];

$sql_first_subject = "SELECT id FROM subjects LIMIT 1";
$result_first_subject = $conn->query($sql_first_subject);
$subject_id = $result_first_subject->fetch_assoc()['id'];

$stmt_assign = $conn->prepare("INSERT INTO teacher_assignments (teacher_id, section_id, subject_id) VALUES (?, ?, ?)");
$stmt_assign->bind_param("iii", $teacher_id, $section_id, $subject_id);
$stmt_assign->execute();

// Seed students
$students = [
    ['full_name' => 'Alice Smith', 'email' => 'alice.smith@example.com', 'password' => 'password123', 'address' => '123 Main St', 'phone' => '555-1234', 'section_id' => 1],
    ['full_name' => 'Bob Johnson', 'email' => 'bob.johnson@example.com', 'password' => 'password123', 'address' => '456 Oak Ave', 'phone' => '555-5678', 'section_id' => 1],
    ['full_name' => 'Charlie Brown', 'email' => 'charlie.brown@example.com', 'password' => 'password123', 'address' => '789 Pine Ln', 'phone' => '555-9012', 'section_id' => null],
    ['full_name' => 'Diana Prince', 'email' => 'diana.prince@example.com', 'password' => 'password123', 'address' => '101 Maple Dr', 'phone' => '555-3456', 'section_id' => null]
];

$stmt_student = $conn->prepare("INSERT INTO students (full_name, email, password, address, phone, section_id) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($students as $student) {
    $password_hash = password_hash($student['password'], PASSWORD_DEFAULT);
    // Use a variable for section_id to handle null
    $s_id = $student['section_id'];
    $stmt_student->bind_param("sssssi", $student['full_name'], $student['email'], $password_hash, $student['address'], $student['phone'], $s_id);
    $stmt_student->execute();
}


echo "Database seeded successfully.\n";

$conn->close();
?>