<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header("Location: index.php");
    exit();
}

include '../includes/database.php';

$student_id = $_SESSION['student_id'];
$student_info = null;

if ($student_id) {
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_info = $result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Parent Panel</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h3>Welcome, <?php echo $_SESSION['parent_name']; ?>!</h3>
            </div>
            <div class="content-area">
                <h4>Your Child's Information</h4>
                <?php if ($student_info): ?>
                    <p><strong>Name:</strong> <?php echo $student_info['full_name']; ?></p>
                    <p><strong>Class:</strong> <?php echo $student_info['class']; ?></p>

                    <h4 style="margin-top: 20px;">Academic Progress</h4>
                    <p>Grades and attendance information coming soon.</p>
                <?php else: ?>
                    <p>Could not find information for the associated student.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>