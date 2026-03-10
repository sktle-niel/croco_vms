<?php
session_start();
require_once __DIR__ . '/../../backend/include.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../auth/admin.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate inputs are not empty
if (empty($email) || empty($password)) {
    header('Location: ../../auth/admin.php?error=' . urlencode('Please fill in all required fields'));
    exit;
}

// Check admin account
$result = checkAdminAccount($email, $password);

if ($result['success']) {
    // Set session variables
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id']        = $result['admin']['id'];
    $_SESSION['admin_email']     = $result['admin']['email'];
    $_SESSION['admin_user_type'] = $result['admin']['user_type'];

    // Redirect back to form with success flag — overlay will handle the rest
    header('Location: ../../auth/admin.php?success=true');
    exit;

} else {
    // Map error codes to readable messages
    $errorMessages = [
        'not_found'      => 'Admin account not found.',
        'wrong_password' => 'Incorrect password. Please try again.',
        'database_error' => 'Database error. Please try again later.',
    ];

    $message = $errorMessages[$result['error']] ?? $result['message'];
    header('Location: ../../auth/admin.php?error=' . urlencode($message));
    exit;
}
?>