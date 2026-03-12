<?php

require_once __DIR__ . '/../../connection/connection.php';

/**
 * Generate a unique random 6-digit vote ID
 * 
 * @param PDO $pdo Database connection
 * @return string Returns unique 6-digit vote ID
 */
function generateVoteId($pdo) {
    do {
        $voteId = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE vote_id = ?");
        $stmt->execute([$voteId]);
    } while ($stmt->fetchColumn() > 0);
    
    return $voteId;
}

/**
 * Check if a user has already voted in an election
 * 
 * @param string $userId The user's ID
 * @param int $electionBatch The election batch ID (default: 1)
 * @return bool Returns true if user has already voted
 */
function hasUserVoted($userId, $electionBatch = 1) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE user_id = ? AND election_batch = ?");
        $stmt->execute([$userId, $electionBatch]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Error checking user vote status: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if a user has voted for a specific position
 * 
 * @param string $userId The user's ID
 * @param string $position The position to check
 * @param int $electionBatch The election batch ID (default: 1)
 * @return bool Returns true if user has voted for this position
 */
function hasUserVotedForPosition($userId, $position, $electionBatch = 1) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE user_id = ? AND position = ? AND election_batch = ?");
        $stmt->execute([$userId, $position, $electionBatch]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Error checking user vote for position: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all votes cast by a user
 * 
 * @param string $userId The user's ID
 * @param int $electionBatch The election batch ID (default: 1)
 * @return array Returns array of user's votes
 */
function getUserVotes($userId, $electionBatch = 1) {
    $votes = [];
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT v.*, c.cand_fullname, c.cand_position, c.cand_partylist 
            FROM votes v 
            LEFT JOIN candidate c ON v.cand_id = c.cand_id 
            WHERE v.user_id = ? AND v.election_batch = ?
        ");
        $stmt->execute([$userId, $electionBatch]);
        $votes = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting user votes: " . $e->getMessage());
    }
    return $votes;
}

/**
 * Cast a vote for a candidate
 * This function prevents:
 * - Duplicate voting (user can't vote twice for same position)
 * - Re-voting (user can't change their vote)
 * 
 * @param string $userId The user's ID
 * @param int $candidateId The candidate's ID
 * @param string $position The position being voted for
 * @param int $electionBatch The election batch ID (default: 1)
 * @return array Returns array with 'success' (bool) and 'message' (string)
 */
function castVote($userId, $candidateId, $position, $electionBatch = 1) {
    $result = ['success' => false, 'message' => ''];
    
    try {
        $pdo = getDBConnection();
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            $result['message'] = 'User not found.';
            return $result;
        }
        
        // Check if candidate exists
        $stmt = $pdo->prepare("SELECT cand_id FROM candidate WHERE cand_id = ?");
        $stmt->execute([$candidateId]);
        if (!$stmt->fetch()) {
            $result['message'] = 'Candidate not found.';
            return $result;
        }
        
        // ANTI-REVOTE: Check if user has already voted for this position
        if (hasUserVotedForPosition($userId, $position, $electionBatch)) {
            $result['message'] = 'You have already voted for this position. You cannot change your vote.';
            return $result;
        }
        
        // Generate unique vote ID
        $voteId = generateVoteId($pdo);
        
        // Insert the vote with all required columns including vote_id
        $stmt = $pdo->prepare("INSERT INTO votes (vote_id, user_id, election_batch, cand_id, position) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$voteId, $userId, $electionBatch, $candidateId, $position]);
        
        // Update user's is_voted status
        $stmt = $pdo->prepare("UPDATE users SET is_voted = 1 WHERE id = ?");
        $stmt->execute([$userId]);
        
        $result['success'] = true;
        $result['message'] = 'Vote cast successfully!';
        $result['vote_id'] = $voteId;
        
    } catch (PDOException $e) {
        error_log("Error casting vote: " . $e->getMessage());
        $result['message'] = 'An error occurred while casting your vote. Please try again.';
    }
    
    return $result;
}

/**
 * Cast multiple votes at once (for submitting all positions together)
 * 
 * @param string $userId The user's ID
 * @param array $votes Array of ['cand_id' => int, 'position' => string]
 * @param int $electionBatch The election batch ID (default: 1)
 * @return array Returns array with 'success' (bool) and 'message' (string)
 */
function castMultipleVotes($userId, $votes, $electionBatch = 1) {
    $result = ['success' => false, 'message' => ''];
    
    // ANTI-REVOTE: Check if user has already voted in this election
    if (hasUserVoted($userId, $electionBatch)) {
        $result['message'] = 'You have already voted in this election. You cannot vote again.';
        return $result;
    }
    
    try {
        $pdo = getDBConnection();
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            $result['message'] = 'User not found.';
            return $result;
        }
        
        // Validate all candidates exist
        foreach ($votes as $vote) {
            $stmt = $pdo->prepare("SELECT cand_id FROM candidate WHERE cand_id = ?");
            $stmt->execute([$vote['cand_id']]);
            if (!$stmt->fetch()) {
                $result['message'] = 'One or more candidates not found.';
                return $result;
            }
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert all votes with unique vote IDs
        $stmt = $pdo->prepare("INSERT INTO votes (vote_id, user_id, election_batch, cand_id, position) VALUES (?, ?, ?, ?, ?)");
        foreach ($votes as $vote) {
            $voteId = generateVoteId($pdo);
            $stmt->execute([$voteId, $userId, $electionBatch, $vote['cand_id'], $vote['position']]);
        }
        
        // Update user's is_voted status
        $stmt = $pdo->prepare("UPDATE users SET is_voted = 1 WHERE id = ?");
        $stmt->execute([$userId]);
        
        // Commit transaction
        $pdo->commit();
        
        $result['success'] = true;
        $result['message'] = 'All votes cast successfully!';
        
    } catch (PDOException $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        error_log("Error casting multiple votes: " . $e->getMessage());
        $result['message'] = 'An error occurred while casting your votes. Please try again.';
    }
    
    return $result;
}

/**
 * Get total votes per candidate
 * 
 * @param int $electionBatch The election batch ID (default: 1)
 * @return array Returns array with candidate ID as key and vote count as value
 */
function getVoteCounts($electionBatch = 1) {
    $counts = [];
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT cand_id, COUNT(*) as vote_count 
            FROM votes 
            WHERE election_batch = ?
            GROUP BY cand_id
        ");
        $stmt->execute([$electionBatch]);
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
            $counts[$row['cand_id']] = $row['vote_count'];
        }
    } catch (PDOException $e) {
        error_log("Error getting vote counts: " . $e->getMessage());
    }
    return $counts;
}

/**
 * Get the active election batch
 * 
 * @return int Returns election batch ID (default: 1)
 */
function getActiveElectionBatch() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT elc_id FROM election_batch WHERE elc_status = 'active' ORDER BY elc_id DESC LIMIT 1");
        return $stmt->fetchColumn() ?: 1;
    } catch (PDOException $e) {
        error_log("Error getting active election batch: " . $e->getMessage());
        return 1;
    }
}

/**
 * Get database schema information
 * 
 * @return array Returns array with table structure info
 */
function getVotesTableSchema() {
    $schema = [];
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("DESCRIBE votes");
        $schema = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting votes table schema: " . $e->getMessage());
    }
    return $schema;
}
