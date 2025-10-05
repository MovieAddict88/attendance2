<?php
// ---
// app/controllers/logout_controller.php
// ---

// The logout process is straightforward:
// 1. Unset all session variables.
// 2. Destroy the session.
// 3. Redirect to the login page.

// Unset all of the session variables.
$_SESSION = [];

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Redirect to the login page with a success message (optional).
// We can use a query parameter for this, but for simplicity, we'll just redirect.
header("Location: {$base_url}/login");
exit;
?>