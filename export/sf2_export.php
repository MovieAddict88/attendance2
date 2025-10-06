<?php
// sf2_export.php

// Start session to check for authentication
session_start();

// No need for Composer's autoloader for CSV

// Include the database connection
require_once __DIR__ . '/../includes/database.php';

// --- Security Check ---
// Ensure the user is logged in as a teacher
if (!isset($_SESSION['teacher_id'])) {
    die('Unauthorized access.');
}

// --- Parameter Validation ---
if (!isset($_GET['section_id']) || !isset($_GET['subject_id']) || !isset($_GET['month']) || !isset($_GET['year'])) {
    die('Missing required parameters.');
}

$section_id = (int)$_GET['section_id'];
$subject_id = (int)$_GET['subject_id'];
$month = (int)$_GET['month'];
$year = (int)$_GET['year'];

// --- Data Fetching (similar to manage_class.php) ---

// Fetch class details
$sql_class_details = "
    SELECT
        g.grade_name,
        s.section_name,
        sub.subject_name
    FROM sections s
    JOIN grades g ON s.grade_id = g.id
    JOIN subjects sub ON sub.id = ?
    WHERE s.id = ?
";
$stmt_class_details = $conn->prepare($sql_class_details);
$stmt_class_details->bind_param("ii", $subject_id, $section_id);
$stmt_class_details->execute();
$result_class_details = $stmt_class_details->get_result();
$class_details = $result_class_details->fetch_assoc();

if (!$class_details) {
    die("Class details not found.");
}

// Fetch students
$sql_students = "SELECT * FROM students WHERE section_id = ? ORDER BY last_name, first_name, middle_name";
$stmt_students = $conn->prepare($sql_students);
$stmt_students->bind_param("i", $section_id);
$stmt_students->execute();
$result_students = $stmt_students->get_result();
$students = $result_students->fetch_all(MYSQLI_ASSOC);

// --- Date and Attendance Logic ---
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$month_dates = [];
for ($day = 1; $day <= $days_in_month; $day++) {
    $date = new DateTime("$year-$month-$day");
    if ((int)$date->format('N') < 6) { // Monday to Friday
        $month_dates[] = $date;
    }
}

$attendance_data = [];
if (!empty($month_dates)) {
    $start_date = (new DateTime("$year-$month-01"))->format('Y-m-d');
    $end_date = (new DateTime("$year-$month-$days_in_month"))->format('Y-m-d');

    $sql_attendance = "
        SELECT student_id, class_date, status
        FROM attendance
        WHERE subject_id = ?
        AND class_date BETWEEN ? AND ?
    ";
    $stmt_attendance = $conn->prepare($sql_attendance);
    $stmt_attendance->bind_param("iss", $subject_id, $start_date, $end_date);
    $stmt_attendance->execute();
    $result_attendance = $stmt_attendance->get_result();

    while ($row = $result_attendance->fetch_assoc()) {
        $attendance_data[$row['student_id']][$row['class_date']] = $row['status'];
    }
}

// --- CSV Generation ---
$month_name = DateTime::createFromFormat('!m', $month)->format('F');
$filename = "SF2_{$class_details['grade_name']}_{$class_details['section_name']}_{$month_name}_{$year}.csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

// --- Write Header Information ---
fputcsv($output, ['School Form 2 (SF2) - Daily Attendance Report of Learners']);
fputcsv($output, []); // Blank line

$school_year = $year . '-' . ($year + 1);
$grade_section = htmlspecialchars($class_details['grade_name']) . ' - ' . htmlspecialchars($class_details['section_name']);

fputcsv($output, ['School ID', '50678']);
fputcsv($output, ['School Name', 'FAGMMMU INSTITUTE']);
fputcsv($output, ['School Year', $school_year]);
fputcsv($output, ['Grade / Section', $grade_section]);
fputcsv($output, ['Month', $month_name]);
fputcsv($output, ['Division', 'CDO']);
fputcsv($output, []); // Blank line

// --- Write Table Header ---
$table_header = ['No.', "LEARNER'S NAME (Last Name, First Name, Middle Name)", 'SEX'];
foreach ($month_dates as $date) {
    $table_header[] = $date->format('M j');
}
$table_header[] = 'TOTAL PRESENT';
$table_header[] = 'TOTAL ABSENT';
$table_header[] = 'REMARKS (%)';
fputcsv($output, $table_header);

// --- Write Student Data and Attendance ---
$count = 1;
foreach ($students as $student) {
    $student_row = [];
    $student_row[] = $count++;
    $student_row[] = strtoupper(htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . ' ' . $student['middle_name']));
    $student_row[] = strtoupper(substr($student['sex'], 0, 1));

    $total_present = 0;
    $total_absent = 0;

    foreach ($month_dates as $d) {
        $date_str = $d->format('Y-m-d');
        $status = $attendance_data[$student['id']][$date_str] ?? '';
        $mark = ''; // Present
        if ($status === 'absent') {
            $mark = 'x';
            $total_absent++;
        } elseif ($status === 'present') {
            $total_present++;
        }
        $student_row[] = $mark;
    }

    $total_school_days_in_month = count($month_dates);
    $remarks = '';
    if ($total_school_days_in_month > 0) {
        $percentage = ($total_present / $total_school_days_in_month) * 100;
        $remarks = number_format($percentage, 2);
    }

    $student_row[] = $total_present;
    $student_row[] = $total_absent;
    $student_row[] = $remarks;

    fputcsv($output, $student_row);
}

fclose($output);
exit();
?>