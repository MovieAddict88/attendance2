<?php
// ---
// app/controllers/upload_controller.php
// ---

// --- Security Check: Ensure the user is logged in ---
if (!isset($_SESSION['user_id'])) {
    // If not logged in, send a forbidden response and exit.
    http_response_code(403);
    exit('You must be logged in to upload files.');
}

// --- Request Method Check ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If the request is not POST, redirect to the dashboard.
    header("Location: {$base_url}/dashboard");
    exit;
}

// --- File Upload Logic ---
if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {

    $user_id = $_SESSION['user_id'];

    // --- Define Target Directory ---
    // Create a user-specific directory to store files securely.
    $target_dir = BASE_PATH . "/storage/uploads/user_{$user_id}/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // --- Sanitize and Prepare File Information ---
    $file_name = basename($_FILES["fileToUpload"]["name"]);
    // Sanitize filename to prevent directory traversal attacks
    $safe_file_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $file_name);

    $target_file = $target_dir . $safe_file_name;
    $file_size = $_FILES["fileToUpload"]["size"];
    $file_type = mime_content_type($_FILES["fileToUpload"]["tmp_name"]);

    // --- Prevent Overwriting: Check if file already exists ---
    $counter = 1;
    while (file_exists($target_file)) {
        $file_parts = pathinfo($safe_file_name);
        $new_filename = $file_parts['filename'] . "_{$counter}." . $file_parts['extension'];
        $target_file = $target_dir . $new_filename;
        $counter++;
    }
    $final_filename = basename($target_file);

    // --- Move the Uploaded File ---
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // --- Store File Metadata in Database ---
        try {
            $pdo = get_db_connection();
            $stmt = $pdo->prepare(
                "INSERT INTO documents (name, file_path, file_size, file_type, user_id) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$final_filename, "user_{$user_id}/" . $final_filename, $file_size, $file_type, $user_id]);

            $_SESSION['success_message'] = "The file " . htmlspecialchars($final_filename) . " has been uploaded.";

        } catch (PDOException $e) {
            // If DB insert fails, delete the uploaded file to prevent orphaned files.
            unlink($target_file);
            $_SESSION['error_message'] = "Database error: Could not save file metadata. " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
    }

} else {
    // --- Handle Upload Errors ---
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
        UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
        UPLOAD_ERR_PARTIAL    => "The uploaded file was only partially uploaded.",
        UPLOAD_ERR_NO_FILE    => "No file was uploaded.",
        UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
        UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
        UPLOAD_ERR_EXTENSION  => "A PHP extension stopped the file upload.",
    ];
    $error_code = $_FILES['fileToUpload']['error'] ?? UPLOAD_ERR_NO_FILE;
    $_SESSION['error_message'] = $upload_errors[$error_code] ?? "An unknown upload error occurred.";
}

// --- Redirect back to the dashboard ---
header("Location: {$base_url}/dashboard");
exit;
?>