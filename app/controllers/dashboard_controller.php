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

    // Determine the current folder from the URL, defaulting to the root (NULL).
    $current_folder_id = isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null;
    $current_folder = null;
    $breadcrumbs = [['name' => 'Home', 'url' => "{$base_url}/dashboard"]];

    // If we are inside a folder, verify it belongs to the user and fetch its details.
    if ($current_folder_id) {
        $stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ? AND user_id = ?");
        $stmt->execute([$current_folder_id, $user_id]);
        $current_folder = $stmt->fetch();

        // If the folder doesn't exist or doesn't belong to the user, redirect.
        if (!$current_folder) {
            $_SESSION['error_message'] = "Folder not found.";
            header("Location: {$base_url}/dashboard");
            exit;
        }

        // --- Build Breadcrumbs ---
        $path_folder = $current_folder;
        $temp_breadcrumbs = [];
        while ($path_folder) {
            $temp_breadcrumbs[] = [
                'name' => $path_folder['name'],
                'url' => "{$base_url}/dashboard?folder_id=" . $path_folder['id']
            ];
            if ($path_folder['parent_id']) {
                $stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ?");
                $stmt->execute([$path_folder['parent_id']]);
                $path_folder = $stmt->fetch();
            } else {
                $path_folder = null;
            }
        }
        $breadcrumbs = array_merge($breadcrumbs, array_reverse($temp_breadcrumbs));
    }

    // Fetch sub-folders of the current folder.
    $folder_stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ? AND parent_id <=> ? ORDER BY name ASC");
    $folder_stmt->execute([$user_id, $current_folder_id]);
    $folders = $folder_stmt->fetchAll();

    // Fetch documents in the current folder.
    $doc_stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? AND folder_id <=> ? ORDER BY name ASC");
    $doc_stmt->execute([$user_id, $current_folder_id]);
    $documents = $doc_stmt->fetchAll();

} catch (PDOException $e) {
    $error_message = "Error fetching data: " . $e->getMessage();
}

// --- Display the Dashboard View ---
$page_title = 'Dashboard';

// All the fetched data ($folders, $documents, $error_message) is now available to the view.
include_once BASE_PATH . '/app/views/dashboard_view.php';