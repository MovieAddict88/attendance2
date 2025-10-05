<?php
// Corrected include path
include __DIR__ . '/../../includes/database.php';

// 1. Create a teacher
$full_name = 'Test Teacher';
$email = 'teacher@example.com';
$password = password_hash('password123', PASSWORD_DEFAULT);
$phone = '1234567890';
$profile_image = '';

// Use INSERT ... ON DUPLICATE KEY UPDATE to avoid errors on re-runs
$sql_teacher = "INSERT INTO teachers (full_name, email, password, phone, profile_image) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE email=VALUES(email)";
$stmt_teacher = $conn->prepare($sql_teacher);
$stmt_teacher->bind_param("sssss", $full_name, $email, $password, $phone, $profile_image);
$stmt_teacher->execute();

// Get teacher_id (handling both insert and update cases)
$teacher_id = $conn->insert_id;
if ($teacher_id == 0) {
    $sql_get_teacher = "SELECT id FROM teachers WHERE email = ?";
    $stmt_get_teacher = $conn->prepare($sql_get_teacher);
    $stmt_get_teacher->bind_param("s", $email);
    $stmt_get_teacher->execute();
    $result_teacher = $stmt_get_teacher->get_result();
    $teacher = $result_teacher->fetch_assoc();
    $teacher_id = $teacher['id'];
}
echo "Teacher seeded with ID: $teacher_id<br>";


// 2. Get a section and subject
$sql_section = "SELECT id FROM sections LIMIT 1";
$result_section = $conn->query($sql_section);
$section = $result_section->fetch_assoc();
$section_id = $section['id'];
echo "Using section ID: $section_id<br>";

$sql_subject = "SELECT id FROM subjects LIMIT 1";
$result_subject = $conn->query($sql_subject);
$subject = $result_subject->fetch_assoc();
$subject_id = $subject['id'];
echo "Using subject ID: $subject_id<br>";

// 3. Assign teacher to section/subject
$sql_assign = "INSERT INTO teacher_assignments (teacher_id, section_id, subject_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE teacher_id=VALUES(teacher_id)";
$stmt_assign = $conn->prepare($sql_assign);
$stmt_assign->bind_param("iii", $teacher_id, $section_id, $subject_id);
$stmt_assign->execute();
echo "Teacher assigned to class.<br>";

// 4. Create a student in that section
$last_name = 'Torrejos';
$first_name = 'Roel';
$middle_name = 'Lim';
$sex = 'M';
$student_email = 'roel.torrejos@example.com';
$student_password = password_hash('password123', PASSWORD_DEFAULT);
$address = '123 Main St';
$student_phone = '555-1234';

$sql_student = "INSERT INTO students (last_name, first_name, middle_name, sex, email, password, address, phone, section_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE email=VALUES(email)";
$stmt_student = $conn->prepare($sql_student);
$stmt_student->bind_param("ssssssssi", $last_name, $first_name, $middle_name, $sex, $student_email, $student_password, $address, $student_phone, $section_id);
$stmt_student->execute();
echo "Student seeded.<br>";

echo "Setup complete.";
?>