<?php
/**
 * Session Checker
 * This file checks if user is logged in before allowing access to protected pages
 */

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // User is not logged in, redirect to login page
    header('Location: ../../auth/form.php');
    exit;
}

// Also check if user has already voted - if so, redirect to home
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // Include the cast.php to get the getActiveElectionBatch function
    require_once __DIR__ . '/../backend/cast/cast.php';
    require_once __DIR__ . '/../connection/connection.php';
    
    try {
        $pdo = getDBConnection();
        $electionBatch = getActiveElectionBatch();
        
        if ($electionBatch) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE voter_id = ? AND election_batch = ?");
            $stmt->execute([$_SESSION['user_id'], $electionBatch]);
            $voteCount = $stmt->fetchColumn();
            
            if ($voteCount > 0) {
                // User has already voted, redirect to home
                header('Location: ../../public/pages/home.php');
                exit;
            }
        }
    } catch (PDOException $e) {
        // If error, allow access (fail open)
        error_log("Error checking vote status: " . $e->getMessage());
    }
}
?>
