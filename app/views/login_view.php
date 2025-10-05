<?php
// ---
// app/views/login_view.php
// ---

// The header partial is included at the top of the page.
include_once BASE_PATH . '/app/views/partials/header.php';
?>

<div class="form-container">
    <h2>Login to Your Account</h2>
    <p>Please enter your credentials to log in.</p>

    <?php
    // Display any error messages passed from the controller
    if (isset($error_message)) {
        echo "<div class='alert alert-error'>" . htmlspecialchars($error_message) . "</div>";
    }
    ?>

    <form action="<?php echo $base_url; ?>/login" method="post">
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-block">Login</button>
    </form>
</div>

<?php
// The footer partial is included at the bottom of the page.
include_once BASE_PATH . '/app/views/partials/footer.php';
?>