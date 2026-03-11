<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../backend/include.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../auth/admin.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    header('Location: ../../auth/admin.php?error=' . urlencode('Please fill in all required fields'));
    exit;
}

$result = checkAdminAccount($email, $password);

if (!$result['success']) {
    $errorMessages = [
        'not_found'      => 'Admin account not found.',
        'wrong_password' => 'Incorrect password. Please try again.',
        'database_error' => 'Database error. Please try again later.',
    ];
    $message = $errorMessages[$result['error']] ?? $result['message'];
    header('Location: ../../auth/admin.php?error=' . urlencode($message));
    exit;
}

$otpResult = sendAdminOTP($email);

if (!$otpResult['success']) {
    header('Location: ../../auth/admin.php?error=' . urlencode('Failed to send OTP: ' . $otpResult['message']));
    exit;
}

$_SESSION['admin_pending_email'] = $email;
$_SESSION['admin_otp_sent']      = true;

header('Location: ../../auth/admin.php?view=otp');
exit;