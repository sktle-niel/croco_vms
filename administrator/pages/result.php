<!-- Results Page -->
<?php
require_once __DIR__ . '/../../backend/include.php';

$stats = getDashboardStats();
$totalVoters = $stats['totalVoters'];
$candidates = getResultsCandidates();
?>

<div class="results-page">
    <div class="results-container">

        <?php if (empty($candidates)): ?>
        <div class="empty-state">
            <i class="fas fa-user-tie"></i>
            <p>No candidates found.</p>
        </div>
        <?php else: ?>
            <?php foreach ($candidates as $c):
$barPct = $totalVoters > 0 ? round(($c['vote_count'] / $totalVoters) * 100) : 0;
            ?>
            <div class="candidate-card">
                <?php if (!empty($c['cand_photo'])): ?>
                    <img src="../../<?php echo htmlspecialchars($c['cand_photo']); ?>"
                         alt="<?php echo htmlspecialchars($c['cand_fullname']); ?>"
                         class="candidate-image"
                         style="width:56px;height:56px;">
                <?php else: ?>
                    <div class="candidate-image" style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;background:#f0f0f0;">
                        <i class="fas fa-user" style="font-size:2rem;color:#aaa;"></i>
                    </div>
                <?php endif; ?>

                <div class="candidate-info">
                    <div class="candidate-name"><?php echo htmlspecialchars($c['cand_fullname']); ?></div>
                    <div class="candidate-position"><?php echo htmlspecialchars($c['cand_position']); ?></div>
                    <?php if (!empty($c['cand_partylist'])): ?>
                        <div class="candidate-partylist"><?php echo htmlspecialchars($c['cand_partylist']); ?></div>
                    <?php endif; ?>
                    <div class="vote-count"><?php echo number_format($c['vote_count']); ?> votes</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $barPct; ?>%;"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>