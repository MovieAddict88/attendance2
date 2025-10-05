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
    // We must join with the users table to ensure the document belongs to the logged-in user.
    $stmt = $pdo->prepare(
        "SELECT * FROM documents WHERE id = ? AND user_id = ?"
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

    if (!file_exists($file_path)) {
        // This case can happen if the file was deleted from the server manually.
        $_SESSION['error_message'] = "Error: The file does not exist on the server.";
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