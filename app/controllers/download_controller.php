<?php
// ---
// app/controllers/download_controller.php
// ---

// --- Security Check: Ensure the user is logged in ---
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page.
    header("Location: {$base_url}/login");
    exit;
}

// --- Input Validation: Check if a document ID is provided ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid download link. No document specified.";
    header("Location: {$base_url}/dashboard");
    exit;
}

$document_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    $pdo = get_db_connection();

    // --- Fetch Document and Verify Permissions ---
    // Check if the user has any permission for this document (view, edit, owner, etc.).
    // This is more flexible than just checking the owner (documents.user_id).
    $stmt = $pdo->prepare(
        "SELECT d.* FROM documents d
         JOIN user_permissions up ON d.id = up.document_id
         WHERE d.id = ? AND up.user_id = ?"
    );
    $stmt->execute([$document_id, $user_id]);
    $document = $stmt->fetch();

    if (!$document) {
        // If no document is found, or it doesn't belong to the user, deny access.
        $_SESSION['error_message'] = "File not found or you do not have permission to access it.";
        header("Location: {$base_url}/dashboard");
        exit;
    }

    // --- File Download Logic ---
    // Construct the full file path from the base storage path and the stored relative path.
    $file_path = BASE_PATH . '/' . $document['file_path'];

    // --- SECURITY CHECK for Path Traversal ---
    // 1. Define the absolute path of the allowed, secure storage directory.
    $storage_base_path = realpath(BASE_PATH . '/storage/uploads');
    // 2. Resolve the absolute path of the requested file.
    $real_file_path = realpath($file_path);

    // 3. Verify that the resolved file path starts with the storage base path.
    // This check ensures that the requested file is within the intended directory
    // and prevents traversal attacks (e.g., using '../' to access parent directories).
    if ($real_file_path === false || strpos($real_file_path, $storage_base_path) !== 0) {
        // If realpath() fails, the file doesn't exist.
        // If strpos() doesn't return 0, the file is outside the allowed directory.
        $_SESSION['error_message'] = "Error: File not found or access denied.";
        header("Location: {$base_url}/dashboard");
        exit;
    }

    // --- Set Headers for File Download ---
    // Clear any previously sent headers.
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Set content type header
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $document['file_type']); // Use the MIME type stored in the DB
    header('Content-Disposition: attachment; filename="' . basename($document['name']) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));

    // Read the file and send it to the browser.
    readfile($file_path);
    exit;

} catch (PDOException $e) {
    // Database error
    $_SESSION['error_message'] = "Database error: Could not retrieve the file.";
    // In a real application, you'd log this error.
    // error_log("Download error for user {$user_id}: " . $e->getMessage());
    header("Location: {$base_url}/dashboard");
    exit;
}
?>