<?php
/**
 * Check Account Function
 * Checks if an account exists and is verified
 * 
 * @param string $schoolID - Student ID or LRN
 * @param string $otp - One Time Password
 * @return array - Returns array with success status and user data or error message
 */
function checkAccount($schoolID, $otp) {
    require_once '../../connection/connection.php';
    
    $pdo = getDBConnection();
    
    try {
        //  ('users' table)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE school_id = :schoolID LIMIT 1");
        $stmt->execute(['schoolID' => $schoolID]);
        $user = $stmt->fetch();
        
        // if user exists
        if (!$user) {
            return [
                'success' => false,
                'error' => 'not_found',
                'message' => 'Account not found. Please register first.'
            ];
        }
        
        // if account is verified
        if ($user['is_verified'] != 1) {
            return [
                'success' => false,
                'error' => 'not_verified',
                'message' => 'Your account is not yet verified. Please wait for admin approval.'
            ];
        }
        
        // Check if OTP matches
        if ($user['otp'] !== $otp) {
            return [
                'success' => false,
                'error' => 'invalid_otp',
                'message' => 'Invalid OTP. Please try again.'
            ];
        }
        
        // return
        return [
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'school_id' => $user['school_id'],
                'name' => $user['full_name'],
                'department' => $user['department']
            ]
        ];
        
    } catch (PDOException $e) {
        error_log("Check Account Error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'database_error',
            'message' => 'Database error. Please try again later.'
        ];
    }
}
?>

