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

$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
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

// Navigation links
$prev_week = $week - 1;
$next_week = $week + 1;
$prev_month = $month - 1;
$next_month = $month + 1;
$prev_year = $year;
$next_year = $year;

if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year--;
}
if ($next_month == 13) {
    $next_month = 1;
    $next_year++;
}

$total_weeks = ceil($days_in_month / 7);

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
                        <a href="?section_id=<?php echo $section_id; ?>&subject_id=<?php echo $subject_id; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&week=<?php echo $prev_week; ?>" <?php if ($week <= 1) echo 'style="visibility: hidden;"'; ?>>&laquo; Previous Week</a>
                        <span><?php echo "$month_name $year - Week $week"; ?></span>
                        <a href="?section_id=<?php echo $section_id; ?>&subject_id=<?php echo $subject_id; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&week=<?php echo $next_week; ?>" <?php if ($week >= $total_weeks) echo 'style="visibility: hidden;"'; ?>>Next Week &raquo;</a>
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
                                        <?php foreach ($week_dates as $d): ?>
                                            <td class="blank"></td>
                                        <?php endforeach; ?>
                                        <td></td> <!-- Total Present -->
                                        <td></td> <!-- Total Absent -->
                                        <td></td> <!-- Remarks -->
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
</body>
</html>