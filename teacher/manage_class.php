<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: index.php");
    exit();
}

include '../includes/database.php';

if (!isset($_GET['section_id']) || !isset($_GET['subject_id'])) {
    header("Location: dashboard.php");
    exit();
}

$section_id = $_GET['section_id'];
$subject_id = $_GET['subject_id'];

// Handle adding students
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_students'])) {
    if (!empty($_POST['student_ids'])) {
        $student_ids = $_POST['student_ids'];
        $sql_update_student = "UPDATE students SET section_id = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update_student);

        foreach ($student_ids as $student_id) {
            $stmt_update->bind_param("ii", $section_id, $student_id);
            $stmt_update->execute();
        }

        // Redirect to the same page to prevent form resubmission
        header("Location: manage_class.php?section_id=$section_id&subject_id=$subject_id&message=students_added");
        exit();
    }
}

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
    echo "Class details not found.";
    exit();
}

// Fetch students in this section
$sql_students = "SELECT * FROM students WHERE section_id = ?";
$stmt_students = $conn->prepare($sql_students);
$stmt_students->bind_param("i", $section_id);
$stmt_students->execute();
$result_students = $stmt_students->get_result();

// Fetch students not in any section
$sql_unassigned_students = "SELECT * FROM students WHERE section_id IS NULL";
$result_unassigned_students = $conn->query($sql_unassigned_students);

// --- Attendance Sheet Logic ---
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$week = isset($_GET['week']) ? (int)$_GET['week'] : 1;

// Boundary checks for year to keep it within 2010-2030
if ($year < 2010) $year = 2010;
if ($year > 2030) $year = 2030;

$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$total_weeks = ceil($days_in_month / 7);

// Ensure week is valid for the given month, cap if necessary
if ($week < 1) $week = 1;
if ($week > $total_weeks) $week = $total_weeks;

// --- Navigation Logic ---
// Next Week Calculation
$next_week_week = $week + 1;
$next_week_month = $month;
$next_week_year = $year;
if ($next_week_week > $total_weeks) {
    $next_week_week = 1;
    $next_week_month++;
    if ($next_week_month > 12) {
        $next_week_month = 1;
        $next_week_year++;
    }
}

// Previous Week Calculation
$prev_week_week = $week - 1;
$prev_week_month = $month;
$prev_week_year = $year;
if ($prev_week_week < 1) {
    $prev_week_month--;
    if ($prev_week_month < 1) {
        $prev_week_month = 12;
        $prev_week_year--;
    }
    $prev_month_days = cal_days_in_month(CAL_GREGORIAN, $prev_week_month, $prev_week_year);
    $prev_week_week = ceil($prev_month_days / 7);
}

// --- Date Calculation for Display ---
$date = new DateTime("$year-$month-01");
$month_name = $date->format('F');

// Calculate week start and end dates
$week_start_day = 1 + (($week - 1) * 7);
$date->setDate($year, $month, $week_start_day);

// Find the first Monday for the week view
$day_of_week = $date->format('N'); // 1 (for Monday) through 7 (for Sunday)
$date->modify('-' . ($day_of_week - 1) . ' days');

$week_dates = [];
for ($i = 0; $i < 5; $i++) { // Monday to Friday
    $week_dates[] = clone $date;
    $date->modify('+1 day');
}

// --- Fetch Attendance Data ---
$attendance_data = [];
if (!empty($week_dates)) {
    $start_date = $week_dates[0]->format('Y-m-d');
    $end_date = end($week_dates)->format('Y-m-d');

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

// --- Fetch Monthly Attendance Summary for Totals and Remarks ---
$first_day_of_month = new DateTime("$year-$month-01");
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
        student_id,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as total_present,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as total_absent
    FROM attendance
    WHERE subject_id = ?
      AND class_date BETWEEN ? AND ?
    GROUP BY student_id
";
$stmt_monthly_summary = $conn->prepare($sql_monthly_summary);
$first_day_str = $first_day_of_month->format('Y-m-d');
$last_day_str = $last_day_of_month->format('Y-m-d');
$stmt_monthly_summary->bind_param("iss", $subject_id, $first_day_str, $last_day_str);
$stmt_monthly_summary->execute();
$result_monthly_summary = $stmt_monthly_summary->get_result();

$monthly_summary_data = [];
while ($row = $result_monthly_summary->fetch_assoc()) {
    $monthly_summary_data[$row['student_id']] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .attendance-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .attendance-nav a, .attendance-nav span {
            padding: 8px 15px;
            text-decoration: none;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .attendance-cell {
            cursor: pointer;
            text-align: center;
            vertical-align: middle;
            font-size: 1.5em;
            width: 50px;
            height: 50px;
        }
        .attendance-cell.present {
            color: green;
        }
        .attendance-cell.absent {
            color: red;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/header.php'; ?>
        <div class="main-content">
            <div class="header">
                <h3>Manage Class: <?php echo htmlspecialchars($class_details['grade_name']); ?> - <?php echo htmlspecialchars($class_details['section_name']); ?></h3>
                <h4>Subject: <?php echo htmlspecialchars($class_details['subject_name']); ?></h4>
            </div>
            <div class="content-area">
                <?php if(isset($_GET['message']) && $_GET['message'] == 'students_added'): ?>
                    <div class="message success">Students added successfully!</div>
                <?php endif; ?>

                <h4>Enrolled Students</h4>
                <div class="table-container attendance-table">
                    <h3 style="text-align:center;">SCHOOL FORM 2 (SF2) - DAILY ATTENDANCE REPORT OF LEARNERS</h3>
                    <div class="attendance-nav">
                        <a href="?section_id=<?php echo $section_id; ?>&subject_id=<?php echo $subject_id; ?>&month=<?php echo $prev_week_month; ?>&year=<?php echo $prev_week_year; ?>&week=<?php echo $prev_week_week; ?>" <?php if ($year <= 2010 && $month == 1 && $week == 1) echo 'style="visibility: hidden;"'; ?>>&laquo; Previous Week</a>
                        <span><?php echo "$month_name $year - Week $week"; ?></span>
                        <a href="?section_id=<?php echo $section_id; ?>&subject_id=<?php echo $subject_id; ?>&month=<?php echo $next_week_month; ?>&year=<?php echo $next_week_year; ?>&week=<?php echo $next_week_week; ?>" <?php if ($next_week_year > 2030) echo 'style="visibility: hidden;"'; ?>>Next Week &raquo;</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2">No.</th>
                                <th rowspan="2" class="name-col">LEARNER'S NAME<br><span class="small">(Last Name, First Name, Middle Name)</span></th>
                                <th rowspan="2">SEX</th>
                                <th colspan="5">Days of the Week</th>
                                <th rowspan="2">TOTAL<br>PRESENT</th>
                                <th rowspan="2">TOTAL<br>ABSENT</th>
                                <th rowspan="2">REMARKS</th>
                            </tr>
                            <tr>
                                <?php foreach ($week_dates as $d): ?>
                                    <th><?php echo $d->format('D') . '<br>' . $d->format('j'); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_students->num_rows > 0): ?>
                                <?php $count = 1; ?>
                                <?php while($row = $result_students->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td class="name-col"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td></td> <!-- Sex -->
                                        <?php foreach ($week_dates as $d):
                                            $date_str = $d->format('Y-m-d');
                                            $status = $attendance_data[$row['id']][$date_str] ?? '';
                                            $icon = '';
                                            if ($status === 'present') {
                                                $icon = '&#10004;'; // Checkmark
                                            } else if ($status === 'absent') {
                                                $icon = '&#10006;'; // X
                                            }
                                        ?>
                                            <td class="attendance-cell <?php echo $status; ?>"
                                                data-student-id="<?php echo $row['id']; ?>"
                                                data-date="<?php echo $date_str; ?>"
                                                data-status="<?php echo $status; ?>">
                                                <?php echo $icon; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <?php
                                            $summary = $monthly_summary_data[$row['id']] ?? ['total_present' => 0, 'total_absent' => 0];
                                            $total_present = $summary['total_present'];
                                            $total_absent = $summary['total_absent'];
                                            $remarks = '';
                                            if ($total_school_days > 0) {
                                                $percentage = ($total_present / $total_school_days) * 100;
                                                $remarks = $total_present . "/" . $total_school_days . "*" . "100" . "=" . number_format($percentage, 2) . '%';
                                            }
                                        ?>
                                        <td id="present-<?php echo $row['id']; ?>"><?php echo $total_present; ?></td>
                                        <td id="absent-<?php echo $row['id']; ?>"><?php echo $total_absent; ?></td>
                                        <td id="remarks-<?php echo $row['id']; ?>"><?php echo $remarks; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php $result_students->data_seek(0); // Reset result set pointer ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11">No students enrolled in this class yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <br>
                    <table style="width:100%; border:1px solid black;">
                        <tr>
                            <td style="width:50%; padding:8px;">Prepared by:<br><br><b>__________________________</b><br><small>Class Adviser</small></td>
                            <td style="width:50%; padding:8px;">Checked by:<br><br><b>__________________________</b><br><small>School Head</small></td>
                        </tr>
                    </table>
                </div>

                <div class="add-students-form" style="margin-top: 30px;">
                    <h4>Add Students to Class</h4>
                    <div style="margin-bottom: 20px;">
                        <a href="add_student.php?section_id=<?php echo $section_id; ?>&subject_id=<?php echo $subject_id; ?>" class="btn" style="width: auto; display: inline-block; text-decoration: none; padding: 10px 15px;">Add New Student</a>
                    </div>
                    <?php if ($result_unassigned_students->num_rows > 0): ?>
                        <form action="manage_class.php?section_id=<?php echo $section_id; ?>&subject_id=<?php echo $subject_id; ?>" method="post">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>ID Number</th>
                                            <th>Full Name</th>
                                            <th>Address</th>
                                            <th>Email</th>
                                            <th>Contact Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result_unassigned_students->fetch_assoc()): ?>
                                            <tr>
                                                <td data-label="Select"><input type="checkbox" name="student_ids[]" value="<?php echo $row['id']; ?>"></td>
                                                <td data-label="ID Number"><?php echo $row['id']; ?></td>
                                                <td data-label="Full Name"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                                <td data-label="Address"><?php echo htmlspecialchars($row['address']); ?></td>
                                                <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td data-label="Contact Number"><?php echo htmlspecialchars($row['phone']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" name="add_students" class="button">Add Selected Students</button>
                        </form>
                    <?php else: ?>
                        <p>No students available to add. All students are assigned to a section.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const subjectId = <?php echo json_encode($subject_id); ?>;

        document.querySelectorAll('.attendance-cell').forEach(cell => {
            cell.addEventListener('click', function () {
                if (this.dataset.busy === 'true') {
                    return;
                }
                this.dataset.busy = 'true';

                const studentId = this.dataset.studentId;
                const classDate = this.dataset.date;
                let currentStatus = this.dataset.status;
                let nextStatus;

                if (currentStatus === '') {
                    nextStatus = 'present';
                } else if (currentStatus === 'present') {
                    nextStatus = 'absent';
                } else {
                    nextStatus = ''; // Back to neutral
                }

                // Update UI immediately
                this.dataset.status = nextStatus;
                this.classList.remove('present', 'absent');
                if (nextStatus === 'present') {
                    this.innerHTML = '&#10004;'; // Checkmark
                    this.classList.add('present');
                } else if (nextStatus === 'absent') {
                    this.innerHTML = '&#10006;'; // X
                    this.classList.add('absent');
                } else {
                    this.innerHTML = '';
                }

                // Send data to the server
                fetch('update_attendance.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        student_id: studentId,
                        subject_id: subjectId,
                        class_date: classDate,
                        status: nextStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.totals) {
                        // Update the totals in the table
                        document.getElementById('present-' + studentId).textContent = data.totals.total_present;
                        document.getElementById('absent-' + studentId).textContent = data.totals.total_absent;
                        document.getElementById('remarks-' + studentId).textContent = data.totals.remarks;
                    } else {
                        // Revert UI on failure
                        console.error('Failed to update attendance');
                        this.dataset.status = currentStatus;
                        this.classList.remove('present', 'absent');
                        if (currentStatus === 'present') {
                            this.innerHTML = '&#10004;';
                            this.classList.add('present');
                        } else if (currentStatus === 'absent') {
                            this.innerHTML = '&#10006;';
                            this.classList.add('absent');
                        } else {
                            this.innerHTML = '';
                        }
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    this.dataset.busy = 'false';
                });
            });
        });
    });
    </script>
</body>
</html>