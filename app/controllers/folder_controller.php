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
$parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
$user_id = $_SESSION['user_id'];
$redirect_url = $parent_id ? "{$base_url}/dashboard?folder_id={$parent_id}" : "{$base_url}/dashboard";

// --- Validation ---
if (empty($folder_name)) {
    $_SESSION['error_message'] = "Folder name cannot be empty.";
} elseif (strlen($folder_name) > 255) {
    $_SESSION['error_message'] = "Folder name is too long.";
} else {
    try {
        $pdo = get_db_connection();

        // If a parent_id is provided, verify it belongs to the user.
        if ($parent_id) {
            $stmt = $pdo->prepare("SELECT id FROM folders WHERE id = ? AND user_id = ?");
            $stmt->execute([$parent_id, $user_id]);
            if ($stmt->fetch() === false) {
                $_SESSION['error_message'] = "Invalid parent folder specified.";
                header("Location: {$base_url}/dashboard");
                exit;
            }
        }

        // Check if a folder with the same name already exists in the same parent folder.
        $stmt = $pdo->prepare("SELECT id FROM folders WHERE name = ? AND user_id = ? AND parent_id <=> ?");
        $stmt->execute([$folder_name, $user_id, $parent_id]);

        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "A folder with the name '" . htmlspecialchars($folder_name) . "' already exists here.";
        } else {
            // Insert the new folder into the database
            $insert_stmt = $pdo->prepare("INSERT INTO folders (name, user_id, parent_id) VALUES (?, ?, ?)");
            $insert_stmt->execute([$folder_name, $user_id, $parent_id]);
            $_SESSION['success_message'] = "Folder '" . htmlspecialchars($folder_name) . "' created successfully.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: Could not create the folder. " . $e->getMessage();
    }
}

// --- Redirect back to the correct folder view ---
header("Location: " . $redirect_url);
exit;
?>