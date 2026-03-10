<!-- Dashboard Page -->
<?php
require_once __DIR__ . '/../../backend/include.php';

$stats           = getDashboardStats();
$totalVoters     = $stats['totalVoters'];
$totalVoted      = $stats['totalVoted'];
$totalPending    = $stats['totalPending'];
$totalCandidates = $stats['totalCandidates'];

// Get candidates with votes
$candidates = getCandidatesWithVotes();

// Calculate max votes and percentage
$maxVotes    = getMaxVotes($candidates);
$votePercent = getVotePercent($totalVoted, $totalVoters);
?>


<div class="dashboard-page">

    <div class="rankings-container">
        <div class="rankings-header">
            <h2>Candidate Rankings</h2>
            <span><?php echo count($candidates); ?> candidates</span>
        </div>

        <?php if (empty($candidates)): ?>
        <div class="empty-state">
            <i class="fas fa-user-tie"></i>
            <p>No candidates found.</p>
        </div>
        <?php else: ?>
            <?php foreach ($candidates as $i => $c):
                $rank    = $i + 1;
                $barPct  = round(($c['vote_count'] / $maxVotes) * 100);
                $medal   = $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : null));
            ?>
            <div class="rank-row">
                <div class="rank-number <?php echo $medal ? '' : 'plain'; ?>">
                    <?php echo $medal ?? '#' . $rank; ?>
                </div>

                <?php if (!empty($c['cand_photo'])): ?>
                    <img src="../../<?php echo htmlspecialchars($c['cand_photo']); ?>"
                         alt="<?php echo htmlspecialchars($c['cand_fullname']); ?>"
                         class="rank-photo">
                <?php else: ?>
                    <div class="rank-photo-placeholder"><i class="fas fa-user"></i></div>
                <?php endif; ?>

                <div class="rank-info">
                    <div class="rank-name"><?php echo htmlspecialchars($c['cand_fullname']); ?></div>
                    <div class="rank-meta">
                        <?php echo htmlspecialchars($c['cand_position']); ?>
                        <?php if (!empty($c['cand_partylist'])): ?>
                            &nbsp;·&nbsp;<?php echo htmlspecialchars($c['cand_partylist']); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="rank-bar-wrap">
                    <div class="rank-bar-fill" style="width: <?php echo $barPct; ?>%;"></div>
                </div>

                <div class="rank-votes">
                    <?php echo number_format($c['vote_count']); ?>
                    <small>votes</small>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>