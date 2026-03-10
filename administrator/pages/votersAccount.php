<!-- Voters Account Page -->
<?php
require_once __DIR__ . '/../../backend/include.php';

$users = getVoters();
?>

<div class="voters-page">
    <div class="data-table-container">
        <div class="table-header">
            <h2>Voters Account List</h2>
            <span><?php echo count($users); ?> voters</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student ID / LRN</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Verified</th>
                    <th>Voted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No voters found.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['school_id']); ?></td>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['department']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $u['is_verified'] ? 'active' : 'inactive'; ?>">
                                <?php echo $u['is_verified'] ? 'Verified' : 'Unverified'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $u['is_voted'] ? 'active' : 'inactive'; ?>">
                                <?php echo $u['is_voted'] ? 'Voted' : 'Not Voted'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>