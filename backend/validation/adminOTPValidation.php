<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../backend/include.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../auth/admin.php');
    exit;
}

$otp   = trim($_POST['otp'] ?? '');
$email = $_SESSION['admin_pending_email'] ?? '';

if (empty($email) || !isset($_SESSION['admin_otp_sent'])) {
    header('Location: ../../auth/admin.php?error=' . urlencode('Session expired. Please log in again.'));
    exit;
}

if (empty($otp)) {
    header('Location: ../../auth/admin.php?view=otp&error=' . urlencode('Please enter the OTP.'));
    exit;
}

// Verify OTP
$verify = verifyAdminOTP($email, $otp);

if (!$verify['success']) {
    header('Location: ../../auth/admin.php?view=otp&error=' . urlencode($verify['message']));
    exit;
}

// OTP verified — create full session
$pdo   = getDBConnection();
$stmt  = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$admin = $stmt->fetch();

$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id']        = $admin['id'];
$_SESSION['admin_email']     = $admin['email'];
$_SESSION['admin_user_type'] = $admin['user_type'];

// Clean up
unset($_SESSION['admin_pending_email']);
unset($_SESSION['admin_otp_sent']);

header('Location: ../../auth/admin.php?success=true');
exit;