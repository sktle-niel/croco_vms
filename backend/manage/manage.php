<?php

require_once __DIR__ . '/../../connection/connection.php';

/**
 * 
 * @return array Returns array of election batch data
 */
function getElectionBatches() {
    $electionBatches = [];
    
    try {
        $pdo = getDBConnection();
        $electionBatches = $pdo->query("SELECT elc_id, elc_name, elc_schoolyear FROM election_batch ORDER BY elc_schoolyear DESC")->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error getting election batches: " . $e->getMessage());
    }
    
    return $electionBatches;
}

/**
 * 
 * @return array Returns array of candidate data with election batch info
 */
function getAllCandidates() {
    $candidates = [];
    
    try {
        $pdo  = getDBConnection();
        $stmt = $pdo->query("
            SELECT c.*, eb.elc_name, eb.elc_schoolyear 
            FROM candidate c 
            LEFT JOIN election_batch eb ON c.cand_electionbatch = eb.elc_id 
            ORDER BY FIELD(c.cand_position, 'President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor', 'P.R.O', 'Senator'), c.cand_fullname
        ");
        $candidates = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error getting all candidates: " . $e->getMessage());
    }
    
    return $candidates;
}

/**
 * 
 * @param string $fullname Candidate full name
 * @param string $position Candidate position
 * @param string $partylist Candidate partylist
 * @param int|null $electionbatch Election batch ID
 * @param string $photoPath Photo path
 * @return bool Returns true on success
 */
function addCandidate($fullname, $position, $partylist, $electionbatch, $photoPath) {
    try {
        $pdo  = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO candidate (cand_fullname, cand_partylist, cand_position, cand_photo, cand_electionbatch) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullname, $partylist, $position, $photoPath, $electionbatch]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error adding candidate: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @param int $candId Candidate ID
 * @return bool Returns true on success
 */
function deleteCandidate($candId) {
    try {
        $pdo  = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM candidate WHERE cand_id = ?");
        $stmt->execute([$candId]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error deleting candidate: " . $e->getMessage());
        return false;
    }
}

/**
 * 
 * @return array Contains message, messageType, electionBatches, and candidates
 */
function processManagePage() {
    $data = [
        'message'          => '',
        'messageType'      => '',
        'electionBatches'  => [],
        'candidates'       => []
    ];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $fullname      = trim($_POST['fullname'] ?? '');
            $partylist     = trim($_POST['partylist'] ?? '');
            $position      = trim($_POST['position'] ?? '');
            $electionbatch = !empty(trim($_POST['electionbatch'] ?? '')) ? intval(trim($_POST['electionbatch'])) : null;
            $photoPath = '';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir  = __DIR__ . '/../../img/candidatesImg/';
                $fileName   = time() . '_' . basename($_FILES['photo']['name']);
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                    $photoPath = 'img/candidatesImg/' . $fileName;
                }
            }

            if (!empty($fullname) && !empty($position)) {
                $result = addCandidate($fullname, $position, $partylist, $electionbatch, $photoPath);
                if ($result) {
                    $_SESSION['message'] = 'Candidate added successfully!';
                    $_SESSION['messageType'] = 'success';
                } else {
                    $_SESSION['message'] = 'Error adding candidate.';
                    $_SESSION['messageType'] = 'error';
                }
            } else {
                $_SESSION['message'] = 'Please fill in required fields.';
                $_SESSION['messageType'] = 'error';
            }
            header('Location: ' . $_SERVER['REQUEST_URI']); 
            exit;
        }

        if ($_POST['action'] === 'delete') {
            $candId = intval($_POST['cand_id'] ?? 0);
            if ($candId > 0) {
                $result = deleteCandidate($candId);
                if ($result) {
                    $_SESSION['message'] = 'Candidate removed successfully!';
                    $_SESSION['messageType'] = 'success';
                } else {
                    $_SESSION['message'] = 'Error deleting candidate.';
                    $_SESSION['messageType'] = 'error';
                }
            }
            header('Location: ' . $_SERVER['REQUEST_URI']); 
            exit;
        }
    }

    if (isset($_SESSION['message'])) {
        $data['message'] = $_SESSION['message'];
        $data['messageType'] = $_SESSION['messageType'];
        unset($_SESSION['message']);
        unset($_SESSION['messageType']);
    }

    // Fetch election batches for dropdown
    $data['electionBatches'] = getElectionBatches();

    // Fetch all candidates
    $data['candidates'] = getAllCandidates();
    
    return $data;
}

