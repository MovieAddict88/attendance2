<?php
// ---
// app/controllers/delete_controller.php
// ---

// --- Security Check: Ensure the user is logged in ---
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('You must be logged in to perform this action.');
}

// --- Request Method Check ---
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id']) || !isset($_GET['type'])) {
    // We expect a GET request with an id and type (e.g., delete?type=document&id=123)
    header("Location: {$base_url}/dashboard");
    exit;
}

// --- Deletion Logic ---
$user_id = $_SESSION['user_id'];
$id = (int)$_GET['id'];
$type = $_GET['type'];

try {
    $pdo = get_db_connection();

    if ($type === 'document') {
        // --- Delete a Document ---

        // First, find the document to ensure it belongs to the current user and get its file path.
        $stmt = $pdo->prepare("SELECT file_path FROM documents WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        $document = $stmt->fetch();

        if ($document) {
            // Document found, proceed with deletion.
            $file_path_on_disk = BASE_PATH . '/storage/uploads/' . $document['file_path'];

            // 1. Delete the physical file.
            if (file_exists($file_path_on_disk)) {
                unlink($file_path_on_disk);
            }

            // 2. Delete the database record.
            $delete_stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
            $delete_stmt->execute([$id]);

            $_SESSION['success_message'] = "File deleted successfully.";
        } else {
            // Document not found or doesn't belong to the user.
            $_SESSION['error_message'] = "File not found or you do not have permission to delete it.";
        }

    } elseif ($type === 'folder') {
        // --- Delete a Folder ---

        // First, verify the folder belongs to the user.
        $stmt = $pdo->prepare("SELECT id FROM folders WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);

        if ($stmt->fetch()) {
            // Folder found.
            // For a robust system, we should handle sub-folders and files within this folder.
            // For this version, we assume we are deleting an empty folder or we need to delete its contents first.

            // Find all documents in this folder.
            $docs_stmt = $pdo->prepare("SELECT id, file_path FROM documents WHERE folder_id = ? AND user_id = ?");
            $docs_stmt->execute([$id, $user_id]);
            $documents_in_folder = $docs_stmt->fetchAll();

            // Delete all associated physical files.
            foreach ($documents_in_folder as $doc) {
                $file_path_on_disk = BASE_PATH . '/storage/uploads/' . $doc['file_path'];
                if (file_exists($file_path_on_disk)) {
                    unlink($file_path_on_disk);
                }
            }

            // Delete all document records from the database that were in this folder.
            $delete_docs_stmt = $pdo->prepare("DELETE FROM documents WHERE folder_id = ?");
            $delete_docs_stmt->execute([$id]);

            // Finally, delete the folder record itself.
            $delete_folder_stmt = $pdo->prepare("DELETE FROM folders WHERE id = ?");
            $delete_folder_stmt->execute([$id]);

            $_SESSION['success_message'] = "Folder and all its contents deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Folder not found or you do not have permission to delete it.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid deletion type specified.";
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: Could not complete the deletion. " . $e->getMessage();
}

// --- Redirect back to the dashboard ---
header("Location: {$base_url}/dashboard");
exit;
?>