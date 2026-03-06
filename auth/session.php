
<?php
/**
 * Session Checker
 * This file checks if user is logged in before allowing access to protected pages
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // User is not logged in, redirect to login page
    header('Location: ../../auth/form.php');
    exit;
}
?>


