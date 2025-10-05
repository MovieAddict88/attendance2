<?php
// ---
// app/views/search_view.php
// ---

// The header contains the navigation and opening body/main tags.
include_once BASE_PATH . '/app/views/partials/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h2><?php echo $page_title; ?></h2>
    </div>

    <?php if (isset($error_message)): ?>
        <div class='alert alert-error'><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if (empty($folders) && empty($documents) && !isset($error_message)): ?>
        <p>No results found for your search term.</p>
    <?php endif; ?>

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
    </div>
</div>

<?php
// The footer contains the closing main/body/html tags.
include_once BASE_PATH . '/app/views/partials/footer.php';
?>