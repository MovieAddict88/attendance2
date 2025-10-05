<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

include '../includes/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

$student_id = $data['student_id'] ?? null;
$subject_id = $data['subject_id'] ?? null;
$class_date = $data['class_date'] ?? null;
$status = $data['status'] ?? ''; // Default to empty string
$teacher_id = $_SESSION['teacher_id'];

if (!$student_id || !$subject_id || !$class_date) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

if (!in_array($status, ['present', 'absent', ''])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status value']);
    exit();
}

// If status is empty, delete the record
if ($status === '') {
    $sql = "DELETE FROM attendance WHERE student_id = ? AND subject_id = ? AND class_date = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("iis", $student_id, $subject_id, $class_date);
} else {
    // Otherwise, insert or update the record
    $sql = "
        INSERT INTO attendance (student_id, subject_id, teacher_id, class_date, status)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("iiiss", $student_id, $subject_id, $teacher_id, $class_date, $status);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update attendance: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>