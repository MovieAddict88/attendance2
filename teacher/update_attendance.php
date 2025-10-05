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
    // After a successful update, fetch the new weekly totals for the student
    $date_obj = new DateTime($class_date);
    $day_of_week = $date_obj->format('N'); // 1 (Mon) to 7 (Sun)

    // Find the Monday of the week
    $start_of_week = clone $date_obj;
    $start_of_week->modify('-' . ($day_of_week - 1) . ' days');

    // Find the Friday of the week
    $end_of_week = clone $start_of_week;
    $end_of_week->modify('+4 days');

    $start_date_str = $start_of_week->format('Y-m-d');
    $end_date_str = $end_of_week->format('Y-m-d');

    $sql_weekly_summary = "
        SELECT
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as total_present,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as total_absent
        FROM attendance
        WHERE student_id = ? AND subject_id = ? AND class_date BETWEEN ? AND ?
    ";
    $stmt_summary = $conn->prepare($sql_weekly_summary);
    $stmt_summary->bind_param("iiss", $student_id, $subject_id, $start_date_str, $end_date_str);
    $stmt_summary->execute();
    $result_summary = $stmt_summary->get_result();
    $summary = $result_summary->fetch_assoc();

    $total_present = $summary['total_present'] ?? 0;
    $total_absent = $summary['total_absent'] ?? 0;
    $remarks = '';

    // There are always 5 school days in the week view
    $total_school_days_in_week = 5;
    if ($total_school_days_in_week > 0) {
        $percentage = ($total_present / $total_school_days_in_week) * 100;
        $remarks = number_format($percentage, 2) . '%';
    }

    echo json_encode([
        'success' => true,
        'totals' => [
            'total_present' => $total_present,
            'total_absent' => $total_absent,
            'remarks' => $remarks
        ]
    ]);

} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update attendance: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>