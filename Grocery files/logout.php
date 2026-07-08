<?php
session_start();

// Determine where to redirect based on who is logging out
$redirect_to = "index.php"; // Default for customers

if (isset($_SESSION['admin_id']) || isset($_SESSION['admin_logged_in'])) {
    $redirect_to = "admin_login.php"; // Redirect staff back to their portal
}

// 1. Clear all session variables
$_SESSION = array();

// 2. Destroy the session cookie for maximum security
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the actual session on the server
session_destroy();

// 4. Redirect to the determined page
header("Location: " . $redirect_to);
exit();
?>