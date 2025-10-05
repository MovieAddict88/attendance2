<?php
// ---
// app/controllers/search_controller.php
// ---

// --- Security Check: Ensure the user is logged in ---
if (!isset($_SESSION['user_id'])) {
    header("Location: {$base_url}/login");
    exit;
}

// --- Get Search Term ---
$search_term = trim($_GET['q'] ?? '');
$user_id = $_SESSION['user_id'];

$folders = [];
$documents = [];

if (empty($search_term)) {
    // If the search term is empty, just show an empty results page.
    $page_title = 'Search';
    $error_message = 'Please enter a search term.';
} else {
    $page_title = 'Search Results for "' . htmlspecialchars($search_term) . '"';
    try {
        $pdo = get_db_connection();

        // --- Search for Folders ---
        $folder_stmt = $pdo->prepare(
            "SELECT * FROM folders WHERE user_id = ? AND name LIKE ? ORDER BY name ASC"
        );
        $folder_stmt->execute([$user_id, '%' . $search_term . '%']);
        $folders = $folder_stmt->fetchAll();

        // --- Search for Documents ---
        $doc_stmt = $pdo->prepare(
            "SELECT * FROM documents WHERE user_id = ? AND name LIKE ? ORDER BY name ASC"
        );
        $doc_stmt->execute([$user_id, '%' . $search_term . '%']);
        $documents = $doc_stmt->fetchAll();

    } catch (PDOException $e) {
        $error_message = "Database error during search: " . $e->getMessage();
    }
}

// --- Display the Search View ---
// The view will display the $folders and $documents arrays.
include_once BASE_PATH . '/app/views/search_view.php';
?>