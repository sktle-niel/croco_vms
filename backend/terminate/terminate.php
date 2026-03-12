<?php

require_once __DIR__ . '../../../connection/connection.php';

/**
 * 
 * @return bool Returns true on success
 */
function terminateUserSession() {
    try {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Clear all session variables
        $_SESSION = array();
        
        // Get session parameters before destroying
        $sessionName = session_name();
        $sessionId = session_id();
        
        // Destroy the session
        session_destroy();
        
        // Delete the session cookie
        if (isset($_COOKIE[$sessionName])) {
            $params = session_get_cookie_params();
            setcookie(
                $sessionName, 
                '', 
                time() - 42000,
                $params['path'], 
                $params['domain'], 
                $params['secure'], 
                $params['httponly']
            );
        }
        
        // Also try to delete session file
        if (!empty($sessionId)) {
            $sessionPath = session_save_path();
            if (!empty($sessionPath)) {
                $sessionFile = $sessionPath . '/sess_' . $sessionId;
                if (file_exists($sessionFile)) {
                    @unlink($sessionFile);
                }
            }
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Error terminating session: " . $e->getMessage());
        return false;
    }
}

/**
 * Log out user and redirect to home page
 * This function destroys the session and redirects to public/pages/home.php
 */
function logoutAfterVote() {
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Clear all session variables
    $_SESSION = array();
    
    // Get session name
    $sessionName = session_name();
    
    // Destroy the session
    session_destroy();
    
    // Delete the session cookie
    if (isset($_COOKIE[$sessionName])) {
        $params = session_get_cookie_params();
        setcookie(
            $sessionName, 
            '', 
            time() - 42000,
            $params['path'], 
            $params['domain'], 
            $params['secure'], 
            $params['httponly']
        );
    }
    
    // Redirect to home page
    header('Location: ../public/pages/home.php');
    exit;
}

/**
 * Check if user is logged in (session active)
 * 
 * @return bool Returns true if user is logged in
 */
function isUserLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user ID
 * 
 * @return int|null Returns user ID if logged in, null otherwise
 */
function getCurrentUserId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user_id'] ?? null;
}

?>
