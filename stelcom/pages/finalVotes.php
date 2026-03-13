<?php
require_once __DIR__ . '/../../backend/include.php';

// tanggalin ko muna tong page

// Get candidates with votes
$candidates = getCandidatesWithVotes();
?>

<div class="dashboard-page">
    <h2>Final Votes</h2>
    <div class="rankings-container">
        <div class="rankings-header">
            <h3>Total Candidates: <?php echo count($candidates); ?></h3>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Candidate</th>
                    <th>Position</th>
                    <th>Partylist</th>
                    <th>Total Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($candidates as $i => $c):
                    $rank = $i + 1;
                ?>
                <tr>
                    <td><?php echo $rank; ?></td>
                    <td><?php echo htmlspecialchars($c['cand_fullname']); ?></td>
                    <td><?php echo htmlspecialchars($c['cand_position']); ?></td>
                    <td><?php echo htmlspecialchars($c['cand_partylist']); ?></td>
                    <td><?php echo number_format($c['vote_count']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button onclick="window.print()" class="btn btn-primary mt-3">
            <i class="fas fa-print"></i> Print Final Votes
        </button>
    </div>
</div>