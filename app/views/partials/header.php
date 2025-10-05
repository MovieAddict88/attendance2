<?php
// ---
// app/views/partials/header.php
// ---

// The base URL is needed for asset paths and links. It's set in index.php.
global $base_url;

// Page title can be set in the controller before including the header.
$page_title = $page_title ?? 'Document Management System';

// A simple check to see if the user is logged in to adjust the navigation.
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<header class="main-header">
    <div class="logo">
        <a href="<?php echo $is_logged_in ? $base_url . '/dashboard' : '#'; ?>">
            <i class="fas fa-file-alt"></i> DocMS
        </a>
    </div>
    <nav class="main-nav">
        <?php if ($is_logged_in): ?>
            <form action="<?php echo $base_url; ?>/search" method="GET" class="search-form">
                <input type="search" name="q" placeholder="Search files & folders..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        <?php endif; ?>
        <ul>
            <?php if ($is_logged_in): ?>
                <li><span>Welcome, <?php echo htmlspecialchars($_SESSION['user_fullname']); ?>!</span></li>
                <li><a href="<?php echo $base_url; ?>/logout" class="logout-btn">Logout</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main class="main-content">