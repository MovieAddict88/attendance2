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
    // After a successful update, fetch the new monthly totals for the student
    $date_obj = new DateTime($class_date);
    $year = $date_obj->format('Y');
    $month = $date_obj->format('m');

    $first_day_of_month = new DateTime("$year-$month-01");
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $last_day_of_month = new DateTime("$year-$month-$days_in_month");

    // Calculate total school days (Mon-Fri) in the month
    $total_school_days = 0;
    $current_day = clone $first_day_of_month;
    while ($current_day <= $last_day_of_month) {
        $day_of_week = $current_day->format('N');
        if ($day_of_week >= 1 && $day_of_week <= 5) { // Monday to Friday
            $total_school_days++;
        }
        $current_day->modify('+1 day');
    }

    $sql_monthly_summary = "
        SELECT
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as total_present,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as total_absent
        FROM attendance
        WHERE student_id = ? AND subject_id = ? AND class_date BETWEEN ? AND ?
    ";
    $stmt_summary = $conn->prepare($sql_monthly_summary);
    $first_day_str = $first_day_of_month->format('Y-m-d');
    $last_day_str = $last_day_of_month->format('Y-m-d');
    $stmt_summary->bind_param("iiss", $student_id, $subject_id, $first_day_str, $last_day_str);
    $stmt_summary->execute();
    $result_summary = $stmt_summary->get_result();
    $summary = $result_summary->fetch_assoc();

    $total_present = $summary['total_present'] ?? 0;
    $total_absent = $summary['total_absent'] ?? 0;
    $remarks = '0.00%';
    if ($total_school_days > 0) {
        $percentage = ($total_present / $total_school_days) * 100;
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