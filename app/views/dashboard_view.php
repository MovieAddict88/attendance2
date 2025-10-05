<?php
// ---
// app/views/dashboard_view.php
// ---

// The header contains the navigation and opening body/main tags.
include_once BASE_PATH . '/app/views/partials/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <nav class="breadcrumbs">
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                <a href="<?php echo $crumb['url']; ?>"><?php echo htmlspecialchars($crumb['name']); ?></a>
                <?php if ($index < count($breadcrumbs) - 1): ?>
                    <span>/</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <div>
            <button id="uploadBtn" class="btn">
                <i class="fas fa-upload"></i> Upload File
            </button>
            <button id="createFolderBtn" class="btn" style="background-color: #ffc107; border-color: #ffc107;">
                <i class="fas fa-folder-plus"></i> Create Folder
            </button>
        </div>
    </div>

    <?php
    // Show session-based feedback messages
    if (isset($_SESSION['success_message'])) {
        echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
        unset($_SESSION['success_message']); // Clear message after displaying
    }
    if (isset($_SESSION['error_message'])) {
        echo "<div class='alert alert-error'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
        unset($_SESSION['error_message']); // Clear message after displaying
    }
    // Show controller-based error messages (e.g., from data fetching)
    if (isset($error_message)) {
        echo "<div class='alert alert-error'>" . htmlspecialchars($error_message) . "</div>";
    }
    ?>

    <div class="file-grid">
        <!-- Display Folders -->
        <?php if (!empty($folders)): ?>
            <?php foreach ($folders as $folder): ?>
                <div class="folder-item">
                    <a href="<?php echo $base_url; ?>/dashboard?folder_id=<?php echo $folder['id']; ?>" class="item-link">
                        <div class="item-icon">&#128193;</div> <!-- Folder emoji icon -->
                        <div class="item-name"><?php echo htmlspecialchars($folder['name']); ?></div>
                    </a>
                    <div class="item-actions">
                        <a href="<?php echo $base_url; ?>/delete?type=folder&id=<?php echo $folder['id']; ?>" onclick="return confirm('Are you sure you want to delete this folder and all its contents?');" title="Delete">
                            &times;
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Display Documents -->
        <?php if (!empty($documents)): ?>
            <?php foreach ($documents as $document): ?>
                <div class="file-item">
                    <div class="item-icon">&#128196;</div> <!-- Document emoji icon -->
                    <div class="item-name" title="<?php echo htmlspecialchars($document['name']); ?>">
                        <a href="<?php echo $base_url; ?>/download?id=<?php echo $document['id']; ?>" title="Download <?php echo htmlspecialchars($document['name']); ?>">
                            <?php echo htmlspecialchars($document['name']); ?>
                        </a>
                    </div>
                    <div class="item-actions">
                         <a href="<?php echo $base_url; ?>/download?id=<?php echo $document['id']; ?>" class="action-icon" title="Download">
                            &#x21E9; <!-- Download icon -->
                        </a>
                        <a href="<?php echo $base_url; ?>/delete?type=document&id=<?php echo $document['id']; ?>" class="action-icon" onclick="return confirm('Are you sure you want to delete this file?');" title="Delete">
                            &times; <!-- Delete icon -->
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Message for empty dashboard -->
        <?php if (empty($folders) && empty($documents) && !isset($error_message)): ?>
            <p>Your dashboard is empty. Upload a file or create a folder to get started.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Upload File Modal -->
<div id="uploadModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h3>Upload a New File</h3>
        <form action="<?php echo $base_url; ?>/upload" method="post" enctype="multipart/form-data">
            <input type="hidden" name="folder_id" value="<?php echo $current_folder_id ?? ''; ?>">
            <div class="form-group">
                <label for="fileToUpload">Select file to upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" required>
            </div>
            <button type="submit" class="btn">Upload</button>
        </form>
    </div>
</div>

<!-- Create Folder Modal -->
<div id="createFolderModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h3>Create a New Folder</h3>
        <form action="<?php echo $base_url; ?>/create_folder" method="post">
            <input type="hidden" name="parent_id" value="<?php echo $current_folder_id ?? ''; ?>">
            <div class="form-group">
                <label for="folderName">Folder Name:</label>
                <input type="text" name="folderName" id="folderName" required>
            </div>
            <button type="submit" class="btn">Create Folder</button>
        </form>
    </div>
</div>

<script>
// --- Simple Modal Handler ---
document.addEventListener('DOMContentLoaded', function() {
    // Get modals
    var uploadModal = document.getElementById("uploadModal");
    var createFolderModal = document.getElementById("createFolderModal");

    // Get buttons that open modals
    var uploadBtn = document.getElementById("uploadBtn");
    var createFolderBtn = document.getElementById("createFolderBtn");

    // Get all <span> elements that close the modals
    var closeBtns = document.getElementsByClassName("close-btn");

    // Open modals
    if(uploadBtn) uploadBtn.onclick = function() { uploadModal.style.display = "block"; }
    if(createFolderBtn) createFolderBtn.onclick = function() { createFolderModal.style.display = "block"; }

    // Close modals with the 'x' button
    for (let btn of closeBtns) {
        btn.onclick = function() {
            this.parentElement.parentElement.style.display = "none";
        }
    }

    // Close modals when clicking outside of them
    window.onclick = function(event) {
        if (event.target == uploadModal) {
            uploadModal.style.display = "none";
        }
        if (event.target == createFolderModal) {
            createFolderModal.style.display = "none";
        }
    }
});
</script>

<?php
// The footer contains the closing main/body/html tags.
include_once BASE_PATH . '/app/views/partials/footer.php';
?>