<?php

require_once __DIR__ . '/../../connection/connection.php';

/**
 * Fetch candidates from the database
 * 
 * @param string $electionBatch Filter by election batch (optional)
 * @param string $position Filter by position (optional)
 * @return array Returns array of candidate data
 */
function getCandidates($electionBatch = '', $position = '') {
    $candidates = [];
    
    try {
        $pdo = getDBConnection();
        
        $sql = "SELECT `cand_id`, `cand_fullname`, `cand_partylist`, `cand_position`, `cand_photo`, `cand_electionbatch` FROM `candidate` WHERE 1=1";
        $params = [];
        
        if (!empty($electionBatch)) {
            $sql .= " AND `cand_electionbatch` = ?";
            $params[] = $electionBatch;
        }
        
        if (!empty($position)) {
            $sql .= " AND `cand_position` = ?";
            $params[] = $position;
        }
        
        $sql .= " ORDER BY `cand_position`, `cand_fullname`";
        
        if (!empty($params)) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $candidates = $stmt->fetchAll();
        } else {
            $candidates = $pdo->query($sql)->fetchAll();
        }
        
    } catch (PDOException $e) {
        error_log("Error getting candidates: " . $e->getMessage());
    }
    
    return $candidates;
}

/**
 * Get all unique election batches from candidates
 * 
 * @return array Returns array of election batch names
 */
function getCandidateElectionBatches() {
    $batches = [];
    
    try {
        $pdo = getDBConnection();
        $batches = $pdo->query("SELECT DISTINCT `cand_electionbatch` FROM `candidate` WHERE `cand_electionbatch` IS NOT NULL AND `cand_electionbatch` != '' ORDER BY `cand_electionbatch` DESC")->fetchAll(PDO::FETCH_COLUMN);
        
    } catch (PDOException $e) {
        error_log("Error getting election batches: " . $e->getMessage());
    }
    
    return $batches;
}

/**
 * Get all unique positions from candidates
 * 
 * @return array Returns array of position names
 */
function getCandidatePositions() {
    $positions = [];
    
    try {
        $pdo = getDBConnection();
        $positions = $pdo->query("SELECT DISTINCT `cand_position` FROM `candidate` WHERE `cand_position` IS NOT NULL AND `cand_position` != '' ORDER BY `cand_position`")->fetchAll(PDO::FETCH_COLUMN);
        
    } catch (PDOException $e) {
        error_log("Error getting candidate positions: " . $e->getMessage());
    }
    
    return $positions;
}

