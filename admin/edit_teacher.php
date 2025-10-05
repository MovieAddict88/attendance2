<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include '../includes/database.php';

$teacher_id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject_taught = $_POST['subject_taught'];

    $profile_image = $_POST['current_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "../uploads/images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $filename = basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = "uploads/images/" . $filename;
        }
    }

    $sql = "UPDATE teachers SET full_name = ?, email = ?, phone = ?, subject_taught = ?, profile_image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $full_name, $email, $phone, $subject_taught, $profile_image, $teacher_id);

    if ($stmt->execute()) {
        header("Location: manage_teachers.php");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
} else {
    $sql = "SELECT * FROM teachers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/header.php'; ?>
        <div class="main-content">
            <div class="header">
                <h3>Edit Teacher</h3>
            </div>
            <div class="form-container">
                <?php if(isset($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
                <form action="edit_teacher.php?id=<?php echo $teacher_id; ?>" method="post" enctype="multipart/form-data">
                    <div class="input-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo $teacher['full_name']; ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $teacher['email']; ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="<?php echo $teacher['phone']; ?>">
                    </div>
                    <div class="input-group">
                        <label for="subject_taught">Subject Taught</label>
                        <input type="text" id="subject_taught" name="subject_taught" value="<?php echo $teacher['subject_taught']; ?>">
                    </div>
                    <div class="input-group">
                        <label for="profile_image">Profile Image</label>
                        <input type="file" id="profile_image" name="profile_image">
                        <input type="hidden" name="current_image" value="<?php echo $teacher['profile_image']; ?>">
                        <?php if (!empty($teacher['profile_image'])): ?>
                            <img src="../<?php echo $teacher['profile_image']; ?>" alt="Profile Image" width="100">
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn">Update Teacher</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>