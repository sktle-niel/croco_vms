<?php
session_start();

require_once '../include.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $schoolID = trim($_POST['SchoolID'] ?? '');
    $otp = trim($_POST['otp'] ?? '');
    
    // Validate inputs
    if (empty($schoolID) || empty($otp)) {
        header('Location: ../../auth/form.php?error=Please+fill+in+all+fields');
        exit;
    }
    
    // Check account using the function
    $result = checkAccount($schoolID, $otp);
    
    if ($result['success']) {
        // Login successful - set session variables
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['school_id'] = $result['user']['school_id'];
        $_SESSION['user_name'] = $result['user']['name'];
        $_SESSION['department'] = $result['user']['department'];
        $_SESSION['logged_in'] = true;
        
        // Redirect to login 
        header('Location: ../../auth/form.php?success=true');
        exit;
    } else {
        // Login failed - redirect with error message
        $errorMessage = urlencode($result['message']);
        
        switch ($result['error']) {
            case 'not_found':
                header('Location: ../../auth/form.php?error=Account+not+found.+Please+register+first');
                break;
            case 'not_verified':
                header('Location: ../../auth/form.php?error=Your+account+is+not+yet+verified.+Please+wait+for+admin+approval');
                break;
            case 'invalid_otp':
                header('Location: ../../auth/form.php?error=Invalid+OTP.+Please+try+again');
                break;
            case 'already_voted':
                header('Location: ../../auth/form.php?error=You+have+already+cast+your+vote');
                break;
            default:
                header('Location: ../../auth/form.php?error=' . $errorMessage);
        }
        exit;
    }
} else {
    // Not a POST request - redirect to login page
    header('Location: ../../auth/form.php');
    exit;
}
?>

