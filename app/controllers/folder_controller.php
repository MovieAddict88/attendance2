<?php
// ---
// app/controllers/folder_controller.php
// ---

// --- Security Check: Ensure the user is logged in ---
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('You must be logged in to create folders.');
}

// --- Request Method Check ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$base_url}/dashboard");
    exit;
}

// --- Folder Creation Logic ---
$folder_name = trim($_POST['folderName'] ?? '');
$user_id = $_SESSION['user_id'];

// --- Validation ---
if (empty($folder_name)) {
    $_SESSION['error_message'] = "Folder name cannot be empty.";
} elseif (strlen($folder_name) > 255) {
    $_SESSION['error_message'] = "Folder name is too long.";
} else {
    try {
        $pdo = get_db_connection();

        // Check if a folder with the same name already exists for this user at the root level
        $stmt = $pdo->prepare("SELECT id FROM folders WHERE name = ? AND user_id = ? AND parent_id IS NULL");
        $stmt->execute([$folder_name, $user_id]);

        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "A folder with the name '" . htmlspecialchars($folder_name) . "' already exists.";
        } else {
            // Insert the new folder into the database
            $insert_stmt = $pdo->prepare("INSERT INTO folders (name, user_id) VALUES (?, ?)");
            $insert_stmt->execute([$folder_name, $user_id]);
            $_SESSION['success_message'] = "Folder '" . htmlspecialchars($folder_name) . "' created successfully.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: Could not create the folder. " . $e->getMessage();
    }
}

// --- Redirect back to the dashboard ---
header("Location: {$base_url}/dashboard");
exit;
?>