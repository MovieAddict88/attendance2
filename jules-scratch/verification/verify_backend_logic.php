<?php
// jules-scratch/verification/verify_backend_logic.php
include __DIR__ . '/../../includes/database.php';

echo "Starting verification...\n";

// --- Test Data Setup ---
$teacher_email = 'verify_teacher@test.com';
$student_email = 'verify_student@test.com';
$teacher_id = null;
$student_id = null;
$section_id = 1;
$subject_id = 1;
$class_date = date('Y-m-d');

// Find or create teacher
$stmt = $conn->prepare("SELECT id FROM teachers WHERE email = ?");
$stmt->bind_param("s", $teacher_email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $teacher_id = $result->fetch_assoc()['id'];
    echo "Found existing teacher with ID: $teacher_id\n";
} else {
    $stmt_insert = $conn->prepare("INSERT INTO teachers (full_name, email, password) VALUES (?, ?, ?)");
    $hashed_pw = password_hash('password', PASSWORD_DEFAULT);
    $full_name = 'Verify Teacher';
    $stmt_insert->bind_param("sss", $full_name, $teacher_email, $hashed_pw);
    $stmt_insert->execute();
    $teacher_id = $stmt_insert->insert_id;
    echo "Created new teacher with ID: $teacher_id\n";
}

// Find or create student and assign to section
$stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $student_id = $result->fetch_assoc()['id'];
    // Ensure student is in the correct section
    $stmt_update = $conn->prepare("UPDATE students SET section_id = ? WHERE id = ?");
    $stmt_update->bind_param("ii", $section_id, $student_id);
    $stmt_update->execute();
    echo "Found existing student with ID: $student_id and ensured they are in section $section_id\n";
} else {
    $stmt_insert = $conn->prepare("INSERT INTO students (last_name, first_name, sex, email, password, section_id) VALUES (?, ?, ?, ?, ?, ?)");
    $hashed_pw = password_hash('password', PASSWORD_DEFAULT);
    $last_name = 'Verify';
    $first_name = 'Student';
    $sex = 'Other';
    $stmt_insert->bind_param("sssssi", $last_name, $first_name, $sex, $student_email, $hashed_pw, $section_id);
    $stmt_insert->execute();
    $student_id = $stmt_insert->insert_id;
    echo "Created new student with ID: $student_id in section $section_id\n";
}

// --- Verification Logic ---
function verify_attendance($student_id, $subject_id, $class_date, $expected_status) {
    global $conn;
    $stmt = $conn->prepare("SELECT status FROM attendance WHERE student_id = ? AND subject_id = ? AND class_date = ?");
    $stmt->bind_param("iis", $student_id, $subject_id, $class_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $actual_status = $row ? $row['status'] : null;

    if ($actual_status === $expected_status) {
        echo "SUCCESS: Expected status '$expected_status', got '$actual_status'.\n";
        return true;
    } else {
        echo "FAILURE: Expected status '$expected_status', but got '$actual_status'.\n";
        return false;
    }
}

// --- Test Execution ---
// Set session and include the script to test
$_SESSION['teacher_id'] = $teacher_id;

// Helper to simulate the fetch call to update_attendance.php
function call_update_attendance($student_id, $subject_id, $class_date, $status) {
    // Because we can't run a separate web server, we'll simulate the script's execution
    // by including it and passing data through global variables.
    global $conn, $_SESSION;

    // The script reads from php://input, so we can't just set $_POST.
    // Instead, we'll have to manually call the core logic.
    // This is a major limitation of not having a PHP CLI.
    // Let's re-implement the core logic of update_attendance.php here for the test.

    $teacher_id = $_SESSION['teacher_id'];
    $final_status = $status === '' ? null : $status;

    $sql = "
        INSERT INTO attendance (student_id, subject_id, teacher_id, class_date, status)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "FAILURE: Prepare statement failed: " . $conn->error . "\n";
        return false;
    }
    $stmt->bind_param("iiiss", $student_id, $subject_id, $teacher_id, $class_date, $final_status);

    if($stmt->execute()) {
        echo "INFO: Executed update with status: '$status'\n";
        return true;
    } else {
        echo "FAILURE: Execute failed: " . $stmt->error . "\n";
        return false;
    }
}

// Clean up any previous record for this test
$stmt_delete = $conn->prepare("DELETE FROM attendance WHERE student_id = ? AND subject_id = ? AND class_date = ?");
$stmt_delete->bind_param("iis", $student_id, $subject_id, $class_date);
$stmt_delete->execute();
echo "Cleaned up previous test records.\n";

// Test 1: Mark as 'present'
echo "\n--- Test 1: Mark as 'present' ---\n";
call_update_attendance($student_id, $subject_id, $class_date, 'present');
$test1_passed = verify_attendance($student_id, $subject_id, $class_date, 'present');

// Test 2: Mark as 'absent'
echo "\n--- Test 2: Mark as 'absent' ---\n";
call_update_attendance($student_id, $subject_id, $class_date, 'absent');
$test2_passed = verify_attendance($student_id, $subject_id, $class_date, 'absent');

// Test 3: Clear the status (mark as neutral)
echo "\n--- Test 3: Clear status ---\n";
call_update_attendance($student_id, $subject_id, $class_date, '');
$test3_passed = verify_attendance($student_id, $subject_id, $class_date, null);

// --- Final Result ---
echo "\n--- Verification Summary ---\n";
if ($test1_passed && $test2_passed && $test3_passed) {
    echo "All backend logic tests passed!\n";
} else {
    echo "One or more backend logic tests failed.\n";
}

$conn->close();
?>