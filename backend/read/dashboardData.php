<?php
/**
 * Dashboard Data Functions
 * 
 * Functions to retrieve dashboard statistics and candidate data
 */

require_once __DIR__ . '/../../connection/connection.php';

/**
 * Get dashboard statistics
 * 
 * @return array Returns associative array with keys: totalVoters, totalVoted, totalPending, totalCandidates
 */
function getDashboardStats() {
    $stats = [
        'totalVoters'     => 0,
        'totalVoted'      => 0,
        'totalPending'    => 0,
        'totalCandidates' => 0
    ];
    
    try {
        $pdo = getDBConnection();
        
        $stats['totalVoters']     = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['totalVoted']      = $pdo->query("SELECT COUNT(*) FROM users WHERE has_voted = 1")->fetchColumn();
        $stats['totalPending']    = $stats['totalVoters'] - $stats['totalVoted'];
        $stats['totalCandidates'] = $pdo->query("SELECT COUNT(*) FROM candidate")->fetchColumn();
        
    } catch (PDOException $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
    }
    
    return $stats;
}

/**
 * Get candidates with their vote counts
 * 
 * @return array Returns array of candidate data with vote counts
 */
function getCandidatesWithVotes() {
    $candidates = [];
    
    try {
        $pdo  = getDBConnection();
        $stmt = $pdo->query("
            SELECT 
                c.cand_id,
                c.cand_fullname,
                c.cand_position,
                c.cand_partylist,
                c.cand_photo,
                COUNT(v.vote_id) AS vote_count
            FROM candidate c
            LEFT JOIN votes v ON v.cand_id = c.cand_id
            GROUP BY c.cand_id
            ORDER BY vote_count DESC, c.cand_fullname ASC
        ");
        $candidates = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error getting candidates with votes: " . $e->getMessage());
    }
    
    return $candidates;
}

/**
 * Get maximum votes from candidates
 * 
 * @param array $candidates Array of candidate data
 * @return int Returns maximum vote count
 */
function getMaxVotes($candidates) {
    if (empty($candidates)) {
        return 1;
    }
    
    $maxVotes = max(array_column($candidates, 'vote_count'));
    return $maxVotes == 0 ? 1 : $maxVotes;
}

/**
 * Calculate vote percentage
 * 
 * @param int $totalVoted Number of people who voted
 * @param int $totalVoters Total number of voters
 * @return int Returns percentage of voters who voted
 */
function getVotePercent($totalVoted, $totalVoters) {
    return $totalVoters > 0 ? round(($totalVoted / $totalVoters) * 100) : 0;
}

/**
 * Get all voters/users
 * 
 * @return array Returns array of user data
 */
function getVoters() {
    $users = [];
    
    try {
        $pdo   = getDBConnection();
        $users = $pdo->query("SELECT school_id, full_name, department, is_verified, is_voted FROM users ORDER BY created_at DESC")->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error getting voters: " . $e->getMessage());
    }
    
    return $users;
}

/**
 * Get candidates with vote counts for results page
 * Ordered by position ASC, then vote count DESC
 * 
 * @return array Returns array of candidate data with vote counts
 */
function getResultsCandidates() {
    $candidates = [];
    
    try {
        $pdo  = getDBConnection();
        $stmt = $pdo->query("
            SELECT 
                c.cand_id,
                c.cand_fullname,
                c.cand_position,
                c.cand_partylist,
                c.cand_photo,
                COUNT(v.vote_id) AS vote_count
            FROM candidate c
            LEFT JOIN votes v ON v.cand_id = c.cand_id
            GROUP BY c.cand_id
            ORDER BY c.cand_position ASC, vote_count DESC
        ");
        $candidates = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error getting results candidates: " . $e->getMessage());
    }
    
    return $candidates;
}

