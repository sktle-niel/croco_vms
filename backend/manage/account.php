<?php

require_once __DIR__ . '/../../connection/connection.php';

/**
 * 
 * @param string $verified Filter by verified status ('verified', 'unverified', or empty for all)
 * @param string $voted Filter by voted status ('voted', 'not_voted', or empty for all)
 * @param string $department Filter by department (empty for all)
 * @param string $search Search by school_id or full_name
 * @param int $limit Number of records per page
 * @param int $offset Offset for pagination
 * @return array Returns array of user data
 */
function getVoters($verified = '', $voted = '', $department = '', $search = '', $limit = 20, $offset = 0) {
    $users = [];
    
    try {
        $pdo = getDBConnection();
        
        $sql = "SELECT id, school_id, full_name, department, is_verified, is_voted FROM users WHERE 1=1";
        $params = [];
        
        if ($verified === 'verified') {
            $sql .= " AND is_verified = 1";
        } elseif ($verified === 'unverified') {
            $sql .= " AND is_verified = 0";
        }
        
        if ($voted === 'voted') {
            $sql .= " AND is_voted = 1";
        } elseif ($voted === 'not_voted') {
            $sql .= " AND is_voted = 0";
        }
        
        if (!empty($department)) {
            $sql .= " AND department = ?";
            $params[] = $department;
        }
        
        if (!empty($search)) {
            $sql .= " AND (school_id LIKE ? OR full_name LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        
        if (!empty($params)) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll();
        } else {
            $users = $pdo->query($sql)->fetchAll();
        }
        
    } catch (PDOException $e) {
        error_log("Error getting voters: " . $e->getMessage());
    }
    
    return $users;
}

/**
 * Get total count of voters with filters
 * 
 * @param string $verified Filter by verified status
 * @param string $voted Filter by voted status
 * @param string $department Filter by department
 * @param string $search Search by school_id or full_name
 * @return int Total count
 */
function getVotersCount($verified = '', $voted = '', $department = '', $search = '') {
    $count = 0;
    
    try {
        $pdo = getDBConnection();
        
        $sql = "SELECT COUNT(*) FROM users WHERE 1=1";
        $params = [];
        
        if ($verified === 'verified') {
            $sql .= " AND is_verified = 1";
        } elseif ($verified === 'unverified') {
            $sql .= " AND is_verified = 0";
        }
        
        if ($voted === 'voted') {
            $sql .= " AND is_voted = 1";
        } elseif ($voted === 'not_voted') {
            $sql .= " AND is_voted = 0";
        }
        
        if (!empty($department)) {
            $sql .= " AND department = ?";
            $params[] = $department;
        }
        
        if (!empty($search)) {
            $sql .= " AND (school_id LIKE ? OR full_name LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        if (!empty($params)) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();
        } else {
            $count = $pdo->query($sql)->fetchColumn();
        }
        
    } catch (PDOException $e) {
        error_log("Error getting voters count: " . $e->getMessage());
    }
    
    return $count;
}

/**
 * Get all departments from users table
 * 
 * @return array Returns array of department names
 */
function getUserDepartments() {
    $departments = [];
    
    try {
        $pdo = getDBConnection();
        $departments = $pdo->query("SELECT DISTINCT department FROM users WHERE department IS NOT NULL AND department != '' ORDER BY department")->fetchAll(PDO::FETCH_COLUMN);
        
    } catch (PDOException $e) {
        error_log("Error getting user departments: " . $e->getMessage());
    }
    
    return $departments;
}

/**
 * Verify or unverify a user account
 * 
 * @param int $userId User ID
 * @param int $status 1 to verify, 0 to unverify
 * @return bool Returns true on success
 */
function verifyUserAccount($userId, $status) {
    try {
        $pdo = getDBConnection();
        $verifiedAt = $status ? date('Y-m-d H:i:s') : null;
        $stmt = $pdo->prepare("UPDATE users SET is_verified = ?, verified_at = ? WHERE id = ?");
        $stmt->execute([$status, $verifiedAt, $userId]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error verifying user: " . $e->getMessage());
        return false;
    }
}

/**
 *  
 * @return array Returns array of admin account data
 */
function getStelcomAccounts() {
    $accounts = [];
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, email, user_type, created_at FROM admins WHERE user_type = 'stelcom' ORDER BY created_at DESC");
        $stmt->execute();
        $accounts = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error fetching stelcom accounts: " . $e->getMessage());
    }
    
    return $accounts;
}

/**
 * 
 * @param string $email Email to check
 * @return bool Returns true if email exists
 */
function emailExists($email) {
    try {
        $pdo = getDBConnection();
        $check = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
        $check->execute([$email]);
        return (bool) $check->fetch();
        
    } catch (PDOException $e) {
        error_log("Error checking email: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @param string $id Admin ID to check
 * @return bool Returns true if ID exists
 */
function adminIdExists($id) {
    try {
        $pdo = getDBConnection();
        $exists = $pdo->prepare("SELECT id FROM admins WHERE id = ?");
        $exists->execute([$id]);
        return (bool) $exists->fetch();
        
    } catch (PDOException $e) {
        error_log("Error checking admin ID: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @return string Returns unique admin ID
 */
function generateUniqueAdminId() {
    do {
        $id = str_pad(random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT);
    } while (adminIdExists($id));
    
    return $id;
}

/**
 * 
 * @param string $email Admin email
 * @param string $password Admin password (plain text, will be hashed)
 * @return bool Returns true on success
 */
function createStelcomAccount($email, $password) {
    try {
        $pdo = getDBConnection();
        
        if (emailExists($email)) {
            return false;
        }
        
        $id = generateUniqueAdminId();
        
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO admins (id, email, password, user_type) VALUES (?, ?, ?, 'stelcom')");
        $stmt->execute([$id, $email, $hashed]);
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Error creating stelcom account: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @param string $id Admin ID
 * @return bool Returns true on success
 */
function deleteStelcomAccount($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ? AND user_type = 'stelcom'");
        $stmt->execute([$id]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error deleting stelcom account: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @param string $id Admin ID
 * @param string $newPassword New password (plain text, will be hashed)
 * @return bool Returns true on success
 */
function resetStelcomPassword($id, $newPassword) {
    try {
        $pdo = getDBConnection();
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ? AND user_type = 'stelcom'");
        $stmt->execute([$hashed, $id]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error resetting stelcom password: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @return array Contains message, messageType, and accounts
 */
function processAccountPage() {
    $data = [
        'message'     => '',
        'messageType' => '',
        'accounts'    => []
    ];
    
    // Handle redirect messages
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] === 'created') {
            $data['message']     = 'Stelcom account created successfully!';
            $data['messageType'] = 'success';
        } elseif ($_GET['msg'] === 'deleted') {
            $data['message']     = 'Account removed successfully.';
            $data['messageType'] = 'success';
        } elseif ($_GET['msg'] === 'reset') {
            $data['message']     = 'Password reset successfully.';
            $data['messageType'] = 'success';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

        // ── Create Stelcom Account ──
        if ($_POST['action'] === 'create_account') {
            $email            = trim($_POST['email'] ?? '');
            $password         = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');

            if (empty($email) || empty($password) || empty($confirm_password)) {
                $data['message']     = 'Please fill in all required fields.';
                $data['messageType'] = 'error';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['message']     = 'Invalid email address.';
                $data['messageType'] = 'error';
            } elseif (strlen($password) < 8) {
                $data['message']     = 'Password must be at least 8 characters.';
                $data['messageType'] = 'error';
            } elseif ($password !== $confirm_password) {
                $data['message']     = 'Passwords do not match.';
                $data['messageType'] = 'error';
            } else {
                if (emailExists($email)) {
                    $data['message']     = 'Email already exists.';
                    $data['messageType'] = 'error';
                } else {
                    $result = createStelcomAccount($email, $password);
                    if ($result) {
                        echo '<script>window.location.href = "' . $_SERVER['PHP_SELF'] . '?msg=created";</script>';
                        exit;
                    } else {
                        $data['message']     = 'Error creating account.';
                        $data['messageType'] = 'error';
                    }
                }
            }
        }

        // ── Delete Account ──
        if ($_POST['action'] === 'delete_account') {
            $id = trim($_POST['account_id'] ?? '');
            if (!empty($id)) {
                $result = deleteStelcomAccount($id);
                if ($result) {
                    echo '<script>window.location.href = "' . $_SERVER['PHP_SELF'] . '?msg=deleted";</script>';
                    exit;
                } else {
                    $data['message']     = 'Error deleting account.';
                    $data['messageType'] = 'error';
                }
            }
        }

        // ── Reset Password ──
        if ($_POST['action'] === 'reset_password') {
            $id           = trim($_POST['account_id'] ?? '');
            $new_password = trim($_POST['new_password'] ?? '');

            if (empty($id) || empty($new_password)) {
                $data['message']     = 'Missing required fields.';
                $data['messageType'] = 'error';
            } elseif (strlen($new_password) < 8) {
                $data['message']     = 'Password must be at least 8 characters.';
                $data['messageType'] = 'error';
            } else {
                $result = resetStelcomPassword($id, $new_password);
                if ($result) {
                    echo '<script>window.location.href = "' . $_SERVER['PHP_SELF'] . '?msg=reset";</script>';
                    exit;
                } else {
                    $data['message']     = 'Error resetting password.';
                    $data['messageType'] = 'error';
                }
            }
        }
    }

    // Fetch all stelcom accounts
    $data['accounts'] = getStelcomAccounts();
    
    return $data;
}

