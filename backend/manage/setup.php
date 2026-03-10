<?php


require_once __DIR__ . '/../../connection/connection.php';

/**
 * 
 * @return array Returns array of partylist data
 */
function getPartylists() {
    $partylists = [];
    
    try {
        $pdo = getDBConnection();
        $partylists = $pdo->query("SELECT * FROM partylist ORDER BY partylist_name")->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error getting partylists: " . $e->getMessage());
    }
    
    return $partylists;
}

/**
 * 
 * @param string $name Partylist name
 * @return bool Returns true on success
 */
function addPartylist($name) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO partylist (partylist_name) VALUES (?)");
        $stmt->execute([$name]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error adding partylist: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @param int $id Partylist ID
 * @return bool Returns true on success
 */
function deletePartylist($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM partylist WHERE partylist_id = ?");
        $stmt->execute([$id]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error deleting partylist: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @return array Returns array of election batch data
 */
function getSetupElectionBatches() {
    $batches = [];
    
    try {
        $pdo = getDBConnection();
        $batches = $pdo->query("SELECT * FROM election_batch ORDER BY elc_schoolyear DESC")->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error getting election batches: " . $e->getMessage());
    }
    
    return $batches;
}

/**
 * 
 * @param string $elc_name Election name
 * @param string $elc_schoolyear School year
 * @param string $elc_status Status (active/inactive)
 * @param string $elc_createdby Created by
 * @return bool Returns true on success
 */
function addElectionBatch($elc_name, $elc_schoolyear, $elc_status, $elc_createdby) {
    try {
        $pdo = getDBConnection();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("INSERT INTO election_batch (elc_name, elc_schoolyear, elc_status, elc_createdby) VALUES (?, ?, ?, ?)");
        $stmt->execute([$elc_name, $elc_schoolyear, $elc_status, $elc_createdby ?: null]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error adding election batch: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @param int $id Election batch ID
 * @return bool Returns true on success
 */
function deleteElectionBatch($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM election_batch WHERE elc_id = ?");
        $stmt->execute([$id]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error deleting election batch: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @return array Returns array of department data
 */
function getDepartments() {
    $departments = [];
    
    try {
        $pdo = getDBConnection();
        $departments = $pdo->query("SELECT * FROM department ORDER BY dept_name")->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error getting departments: " . $e->getMessage());
    }
    
    return $departments;
}

/**
 * 
 * @param string $dept Department name
 * @return bool Returns true on success
 */
function addDepartment($dept) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO department (dept_name) VALUES (?)");
        $stmt->execute([$dept]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error adding department: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @param int $id Department ID
 * @return bool Returns true on success
 */
function deleteDepartment($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM department WHERE dept_id = ?");
        $stmt->execute([$id]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error deleting department: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @return array Returns array with partylists, batches, and departments
 */
function getSetupData() {
    $data = [
        'partylists'  => [],
        'batches'     => [],
        'departments' => []
    ];
    
    try {
        $pdo = getDBConnection();
        $data['partylists']  = $pdo->query("SELECT * FROM partylist ORDER BY partylist_name")->fetchAll();
        $data['batches']     = $pdo->query("SELECT * FROM election_batch ORDER BY elc_schoolyear DESC")->fetchAll();
        $data['departments'] = $pdo->query("SELECT * FROM department ORDER BY dept_name")->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error getting setup data: " . $e->getMessage());
    }
    
    return $data;
}

/**
\ * 
 * Handles all POST requests and returns message, messageType, partylists, batches, and departments
 * @return array Contains message, messageType, partylists, batches, and departments
 */
function processSetupPage() {
    $data = [
        'message'     => '',
        'messageType' => '',
        'partylists'  => [],
        'batches'     => [],
        'departments' => []
    ];
    
    if (isset($_SESSION['flash_message'])) {
        $data['message']     = $_SESSION['flash_message'];
        $data['messageType'] = $_SESSION['flash_type'];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

        $redirect = strtok($_SERVER['REQUEST_URI'], '?') . '?page=setup';

        if ($_POST['action'] === 'add_partylist') {
            $name = trim($_POST['partylist_name'] ?? '');
            if (!empty($name)) {
                $result = addPartylist($name);
                if ($result) {
                    $_SESSION['flash_message'] = 'Partylist added successfully!';
                    $_SESSION['flash_type']    = 'success';
                } else {
                    $_SESSION['flash_message'] = 'Error adding partylist.';
                    $_SESSION['flash_type']    = 'error';
                }
            } else {
                $_SESSION['flash_message'] = 'Partylist name is required.';
                $_SESSION['flash_type']    = 'error';
            }
            header('Location: ' . $redirect); exit;
        }

        if ($_POST['action'] === 'delete_partylist') {
            $id = intval($_POST['partylist_id'] ?? 0);
            if ($id > 0) {
                $result = deletePartylist($id);
                if ($result) {
                    $_SESSION['flash_message'] = 'Partylist removed successfully!';
                    $_SESSION['flash_type']    = 'success';
                } else {
                    $_SESSION['flash_message'] = 'Error deleting partylist.';
                    $_SESSION['flash_type']    = 'error';
                }
            }
            header('Location: ' . $redirect); exit;
        }

        if ($_POST['action'] === 'add_batch') {
            $elc_name       = trim($_POST['elc_name'] ?? '');
            $elc_schoolyear = trim($_POST['elc_schoolyear'] ?? '');
            $elc_status     = trim($_POST['elc_status'] ?? 'active');
            $elc_createdby  = trim($_POST['elc_createdby'] ?? '');

            if (!empty($elc_name) && !empty($elc_schoolyear)) {
                $result = addElectionBatch($elc_name, $elc_schoolyear, $elc_status, $elc_createdby);
                if ($result) {
                    $_SESSION['flash_message'] = 'Election batch added successfully!';
                    $_SESSION['flash_type']    = 'success';
                } else {
                    $_SESSION['flash_message'] = 'Error adding election batch.';
                    $_SESSION['flash_type']    = 'error';
                }
            } else {
                $_SESSION['flash_message'] = 'Election name and school year are required.';
                $_SESSION['flash_type']    = 'error';
            }
            header('Location: ' . $redirect); exit;
        }

        if ($_POST['action'] === 'delete_batch') {
            $id = intval($_POST['batch_id'] ?? 0);
            if ($id > 0) {
                $result = deleteElectionBatch($id);
                if ($result) {
                    $_SESSION['flash_message'] = 'Election batch removed successfully!';
                    $_SESSION['flash_type']    = 'success';
                } else {
                    $_SESSION['flash_message'] = 'Error deleting election batch.';
                    $_SESSION['flash_type']    = 'error';
                }
            }
            header('Location: ' . $redirect); exit;
        }

        if ($_POST['action'] === 'add_department') {
            $dept = trim($_POST['dept_name'] ?? '');
            if (!empty($dept)) {
                $result = addDepartment($dept);
                if ($result) {
                    $_SESSION['flash_message'] = 'Department added successfully!';
                    $_SESSION['flash_type']    = 'success';
                } else {
                    $_SESSION['flash_message'] = 'Error adding department.';
                    $_SESSION['flash_type']    = 'error';
                }
            } else {
                $_SESSION['flash_message'] = 'Department name is required.';
                $_SESSION['flash_type']    = 'error';
            }
            header('Location: ' . $redirect); exit;
        }

        if ($_POST['action'] === 'delete_department') {
            $id = intval($_POST['dept_id'] ?? 0);
            if ($id > 0) {
                $result = deleteDepartment($id);
                if ($result) {
                    $_SESSION['flash_message'] = 'Department removed successfully!';
                    $_SESSION['flash_type']    = 'success';
                } else {
                    $_SESSION['flash_message'] = 'Error deleting department.';
                    $_SESSION['flash_type']    = 'error';
                }
            }
            header('Location: ' . $redirect); exit;
        }
    }

    // Fetch all setup data
    $data['partylists']  = getPartylists();
    $data['batches']     = getSetupElectionBatches();
    $data['departments'] = getDepartments();
    
    return $data;
}

