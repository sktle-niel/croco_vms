<?php
session_start();
require_once __DIR__ . '/../../backend/include.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../auth/form.php');
    exit;
}

$schoolID = trim($_POST['SchoolID'] ?? '');
$otp      = trim($_POST['otp'] ?? '');

// Validate inputs are not empty
if (empty($schoolID) || empty($otp)) {
    header('Location: ../../auth/form.php?error=Please+fill+in+all+required+fields');
    exit;
}

// Check account using existing checkAccount function
$result = checkAccount($schoolID, $otp);

if ($result['success']) {
    // Set session variables
    $_SESSION['logged_in']   = true;
    $_SESSION['user_id']     = $result['user']['id'];
    $_SESSION['school_id']   = $result['user']['school_id'];
    $_SESSION['name']        = $result['user']['name'];
    $_SESSION['department']  = $result['user']['department'];

    // Redirect back to form with success flag — overlay will handle the rest
    header('Location: ../../auth/form.php?success=true');
    exit;

} else {
    // Map error codes to readable messages
    $errorMessages = [
        'not_found'      => 'Account not found. Please register first.',
        'not_verified'   => 'Your account is not yet verified. Please wait for admin approval.',
        'invalid_otp'    => 'Invalid OTP. Please try again.',
        'database_error' => 'Database error. Please try again later.',
    ];

    $message = $errorMessages[$result['error']] ?? $result['message'];
    header('Location: ../../auth/form.php?error=' . urlencode($message));
    exit;
}
?>