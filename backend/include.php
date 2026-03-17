<?php

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../connection/connection.php';

// Include checkAccount function
require_once __DIR__ . '/read/checkAccount.php';

// Include checkAdminAccount function
require_once __DIR__ . '/read/checkAdminAccount.php';

// Include dashboard data functions
require_once __DIR__ . '/read/dashboardData.php';

// Include setup functions first (contains getPartylists and other shared functions)
require_once __DIR__ . '/manage/setup.php';

// Include manage candidate functions
require_once __DIR__ . '/manage/manage.php';

// Include account management functions
require_once __DIR__ . '/manage/account.php';

// Include TOTP and Mailer
require_once __DIR__ . '/config/mailer.php';
require_once __DIR__ . '/otp/adminOtp.php';

?>
