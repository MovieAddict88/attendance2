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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class</title>
    <link rel="stylesheet" href="/css/style.css">
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
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Number</th>
                                <th>Full Name</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_students->num_rows > 0): ?>
                                <?php while($row = $result_students->fetch_assoc()): ?>
                                    <tr>
                                        <td data-label="ID Number"><?php echo $row['id']; ?></td>
                                        <td data-label="Full Name"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td data-label="Address"><?php echo htmlspecialchars($row['address']); ?></td>
                                        <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td data-label="Contact Number"><?php echo htmlspecialchars($row['phone']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No students enrolled in this class yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
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