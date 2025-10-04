<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include '../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject_taught = $_POST['subject_taught'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);


    $sql = "INSERT INTO teachers (full_name, email, phone, subject_taught, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $full_name, $email, $phone, $subject_taught, $password);

    if ($stmt->execute()) {
        header("Location: manage_teachers.php");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teacher</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_teachers.php" class="active">Manage Teachers</a></li>
                <li><a href="manage_students.php">Manage Students</a></li>
                <li><a href="manage_parents.php">Manage Parents</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h3>Add New Teacher</h3>
            </div>
            <div class="form-container">
                <?php if(isset($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
                <form action="add_teacher.php" method="post">
                    <div class="input-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone">
                    </div>
                    <div class="input-group">
                        <label for="subject_taught">Subject Taught</label>
                        <input type="text" id="subject_taught" name="subject_taught">
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn">Add Teacher</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>