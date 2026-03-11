<?php
/**
 * 
 * @param string $email
 * @param string $password
 * @return array 
 */
function checkAdminAccount($email, $password) {
    require_once '../../connection/connection.php';
    
    $pdo = getDBConnection();
    
    try {
        // Check admin table
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();
        
        // If admin not found
        if (!$admin) {
            return [
                'success' => false,
                'error' => 'not_found',
                'message' => 'Admin account not found.'
            ];
        }
        
        // Check if password matches (using password_verify for hashed passwords)
        if (!password_verify($password, $admin['password'])) {
            return [
                'success' => false,
                'error' => 'wrong_password',
                'message' => 'Incorrect password. Please try again.'
            ];
        }
        
        // Return admin data
        return [
            'success' => true,
            'admin' => [
                'id' => $admin['id'],
                'email' => $admin['email'],
                'user_type' => $admin['user_type'] ?? 'admin'
            ]
        ];
        
    } catch (PDOException $e) {
        error_log("Check Admin Account Error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'database_error',
            'message' => 'Database error. Please try again later.'
        ];
    }
}
?>
