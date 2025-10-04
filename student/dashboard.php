<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

include '../includes/database.php';

$student_id = $_SESSION['student_id'];

// For demonstration, we'll just show student's own info.
// A real app would have tables for grades, attendance, etc.
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/header.php'; ?>
        <div class="main-content">
            <div class="header">
                <h3>Welcome, <?php echo $_SESSION['student_name']; ?>!</h3>
            </div>
            <div class="content-area">
                <h4>Your Information</h4>
                <p><strong>Name:</strong> <?php echo $student['full_name']; ?></p>
                <p><strong>Email:</strong> <?php echo $student['email']; ?></p>
                <p><strong>Class:</strong> <?php echo $student['class']; ?></p>

                <h4 style="margin-top: 20px;">Your Grades</h4>
                <p>Grades functionality coming soon.</p>

                <h4 style="margin-top: 20px;">Your Attendance</h4>
                <p>Attendance functionality coming soon.</p>
            </div>
        </div>
    </div>
</body>
</html>