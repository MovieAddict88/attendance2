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

// --- 1. Create a Teacher ---
$teacher_email = 'teacher@test.com';
$teacher_pass = password_hash('password', PASSWORD_DEFAULT);
$sql_teacher = "INSERT INTO teachers (full_name, email, password) VALUES ('Test Teacher', ?, ?)";
$stmt_teacher = $conn->prepare($sql_teacher);
$stmt_teacher->bind_param("ss", $teacher_email, $teacher_pass);
if ($stmt_teacher->execute()) {
    $teacher_id = $stmt_teacher->insert_id;
    echo "Teacher created successfully. ID: $teacher_id\n";
} else {
    // If teacher already exists, get their ID
    $sql_get_teacher = "SELECT id FROM teachers WHERE email = ?";
    $stmt_get_teacher = $conn->prepare($sql_get_teacher);
    $stmt_get_teacher->bind_param("s", $teacher_email);
    $stmt_get_teacher->execute();
    $result_teacher = $stmt_get_teacher->get_result();
    if($result_teacher->num_rows > 0) {
        $teacher_id = $result_teacher->fetch_assoc()['id'];
        echo "Teacher already exists. ID: $teacher_id\n";
    } else {
        die("Error creating or finding teacher: " . $conn->error);
    }
}


// --- 2. Get a Grade and Section ---
// Assuming 'Grade 1' and 'Section A' exist from the initial setup
$sql_section = "SELECT s.id FROM sections s JOIN grades g ON s.grade_id = g.id WHERE g.grade_name = 'Grade 1' AND s.section_name = 'Section A' LIMIT 1";
$result_section = $conn->query($sql_section);
if ($result_section->num_rows > 0) {
    $section_id = $result_section->fetch_assoc()['id'];
    echo "Found Section ID: $section_id\n";
} else {
    die("Could not find section 'Grade 1 - Section A'. Please check if database is seeded.\n");
}

// --- 3. Create a Student and Assign to Section ---
$student_email = 'student@test.com';
$student_pass = password_hash('password', PASSWORD_DEFAULT);
$sql_student = "INSERT INTO students (full_name, email, password, section_id) VALUES ('Test Student', ?, ?, ?)";
$stmt_student = $conn->prepare($sql_student);
$stmt_student->bind_param("ssi", $student_email, $student_pass, $section_id);
if ($stmt_student->execute()) {
    $student_id = $stmt_student->insert_id;
    echo "Student created and assigned to section. ID: $student_id\n";
} else {
    // If student already exists, get their ID
    $sql_get_student = "SELECT id FROM students WHERE email = ?";
    $stmt_get_student = $conn->prepare($sql_get_student);
    $stmt_get_student->bind_param("s", $student_email);
    $stmt_get_student->execute();
    $result_student = $stmt_get_student->get_result();
    if($result_student->num_rows > 0) {
        $student_id = $result_student->fetch_assoc()['id'];
        echo "Student already exists. ID: $student_id\n";
    } else {
        die("Error creating or finding student: " . $conn->error);
    }
}


// --- 4. Get a Subject ---
// Assuming 'Mathematics' exists from the initial setup
$sql_subject = "SELECT id FROM subjects WHERE subject_name = 'Mathematics' LIMIT 1";
$result_subject = $conn->query($sql_subject);
if ($result_subject->num_rows > 0) {
    $subject_id = $result_subject->fetch_assoc()['id'];
    echo "Found Subject ID: $subject_id\n";
} else {
    die("Could not find subject 'Mathematics'. Please check if database is seeded.\n");
}

// --- 5. Assign Teacher to Class (Section + Subject) ---
$sql_assign = "INSERT INTO teacher_assignments (teacher_id, section_id, subject_id) VALUES (?, ?, ?)";
$stmt_assign = $conn->prepare($sql_assign);
$stmt_assign->bind_param("iii", $teacher_id, $section_id, $subject_id);
if ($stmt_assign->execute()) {
    echo "Teacher successfully assigned to class.\n";
} else {
    echo "Teacher assignment might already exist.\n";
}

$stmt_teacher->close();
$stmt_student->close();
$stmt_assign->close();
$conn->close();

echo "Seeding complete.\n";
?>