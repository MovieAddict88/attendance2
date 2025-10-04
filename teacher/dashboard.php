<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: index.php");
    exit();
}

include '../includes/database.php';

// Assuming a teacher might be assigned to a class, let's fetch students of a specific class.
// This is a simplified example. A more complex system might have a teachers_classes pivot table.
// For now, let's just list all students.
$sql_students = "SELECT * FROM students";
$result_students = $conn->query($sql_students);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Teacher Panel</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h3>Welcome, <?php echo $_SESSION['teacher_name']; ?>!</h3>
            </div>
            <div class="content-area">
                <h4>Your Students</h4>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Class</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_students->num_rows > 0): ?>
                                <?php while($row = $result_students->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['full_name']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['class']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No students found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>