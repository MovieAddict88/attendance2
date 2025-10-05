<?php
// ---
// app/controllers/dashboard_controller.php
// ---

// --- Security Check: Ensure the user is logged in ---
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page.
    header("Location: {$base_url}/login");
    exit;
}

// --- Data Fetching ---
try {
    $pdo = get_db_connection();
    $user_id = $_SESSION['user_id'];

    // For now, we fetch all documents and folders for the user.
    // A future enhancement would be to support nested folders and pagination.

    // Fetch all folders owned by the user
    $folder_stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ? ORDER BY name ASC");
    $folder_stmt->execute([$user_id]);
    $folders = $folder_stmt->fetchAll();

    // Fetch all documents owned by the user (that are not in a sub-folder for this basic view)
    $doc_stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? AND folder_id IS NULL ORDER BY name ASC");
    $doc_stmt->execute([$user_id]);
    $documents = $doc_stmt->fetchAll();

} catch (PDOException $e) {
    // If there's a database error, show a generic error message.
    // In a real application, you'd log this error for debugging.
    $error_message = "Error fetching data: " . $e->getMessage();
}

// --- Display the Dashboard View ---
$page_title = 'Dashboard';

// All the fetched data ($folders, $documents, $error_message) is now available to the view.
include_once BASE_PATH . '/app/views/dashboard_view.php';