<?php
require_once '../../connection/connection.php';

header('Content-Type: application/json');

function createAccount($schoolID, $fullName, $department) {
    $pdo = getDBConnection();
    
    // Check if school_id already exists
    $checkStmt = $pdo->prepare("SELECT school_id FROM users WHERE school_id = ?");
    $checkStmt->execute([$schoolID]);
    $checkStmt->fetch();
    
    if ($checkStmt->rowCount() > 0) {
        return ['success' => false, 'error' => 'exists'];
    }
    
    // Generate 7-digit user ID
    $userID = str_pad(random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
    
    // Generate 6-digit OTP (plain text)
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (id, school_id, full_name, department, otp, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$userID, $schoolID, $fullName, $department, $otp]);
        
        return ['success' => true, 'otp' => $otp, 'user_id' => $userID, 'school_id' => $schoolID];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Failed to create account'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schoolID = trim($_POST['SchoolID'] ?? '');
    $fullName = trim($_POST['AccountName'] ?? '');
    $department = trim($_POST['Department'] ?? '');
    
    if (empty($schoolID) || empty($fullName) || empty($department)) {
        echo json_encode(['success' => false, 'error' => 'empty']);
        exit;
    }
    
    $result = createAccount($schoolID, $fullName, $department);
    
    echo json_encode($result);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;
?>
