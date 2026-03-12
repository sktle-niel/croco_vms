<?php

// Set headers for JSON response
header('Content-Type: application/json');

// Start session to get current session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the session file path before destroying
$sessionFile = session_save_path();

// Terminate the user session
try {
    // Clear all session variables
    $_SESSION = array();
    
    // Get session name and ID before destroying
    $sessionName = session_name();
    $sessionId = session_id();
    
    // Destroy the session
    session_destroy();
    
    // Try to destroy the session file
    if (!empty($sessionId)) {
        $sessionFilePath = session_save_path() . '/sess_' . $sessionId;
        if (file_exists($sessionFilePath)) {
            @unlink($sessionFilePath);
        }
    }
    
    // Start a new empty session to prevent issues
    session_start();
    session_unset();
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Session terminated successfully'
    ]);
} catch (Exception $e) {
    error_log("Error terminating session: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to terminate session'
    ]);
}
