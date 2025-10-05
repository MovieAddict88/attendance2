<?php
// ---
// app/controllers/login_controller.php
// ---

// If the user is already logged in, redirect them to the dashboard immediately.
if (isset($_SESSION['user_id'])) {
    header("Location: {$base_url}/dashboard");
    exit;
}

// This controller handles both showing the form (GET) and processing the login (POST).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Handle Login Submission ---
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username/email and password.';
    } else {
        try {
            $pdo = get_db_connection();

            // Prepare a statement to find the user by username or email.
            $stmt = $pdo->prepare("SELECT id, username, password, fullname FROM users WHERE username = :user OR email = :user");
            $stmt->execute(['user' => $username]);
            $user = $stmt->fetch();

            // Verify the user exists and the password is correct.
            if ($user && password_verify($password, $user['password'])) {
                // --- Login Successful ---

                // Regenerate the session ID to prevent session fixation attacks.
                session_regenerate_id(true);

                // Store user information in the session.
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_fullname'] = $user['fullname'];

                // Redirect to the main application dashboard.
                header("Location: {$base_url}/dashboard");
                exit;
            } else {
                // --- Login Failed ---
                $error_message = 'Invalid credentials. Please try again.';
            }
        } catch (PDOException $e) {
            // In a production environment, you would log this error.
            $error_message = 'A database error occurred. Please try again later.';
        }
    }
}

// --- Display Login Page ---
// If it's a GET request or if login fails, the script continues and includes the view.
$page_title = 'Login';
include_once BASE_PATH . '/app/views/login_view.php';