<?php
require_once __DIR__ . '/../../backend/cast/cast.php';
require_once __DIR__ . '/../../auth/session.php';

// Set response header to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'You must be logged in to vote.'
    ]);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method.'
    ]);
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Get election batch
$electionBatch = getActiveElectionBatch();

// Get posted data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['votes']) || empty($input['votes'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'No votes provided.'
    ]);
    exit;
}

$votes = $input['votes'];

// Validate votes structure
foreach ($votes as $vote) {
    if (!isset($vote['cand_id']) || !isset($vote['position'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid vote data. Each vote must have cand_id and position.'
        ]);
        exit;
    }
}

// Check if user has already voted (ANTI-REVOTE)
if (hasUserVoted($userId, $electionBatch)) {
    echo json_encode([
        'success' => false, 
        'message' => 'You have already voted in this election. You cannot vote again.'
    ]);
    exit;
}

// Cast the votes
$result = castMultipleVotes($userId, $votes, $electionBatch);

echo json_encode($result);
