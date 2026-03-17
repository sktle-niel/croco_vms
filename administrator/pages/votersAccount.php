<!DOCTYPE html>
<!-- Voters Account Page -->
<?php
require_once __DIR__ . '/../../backend/include.php';

// Handle verify/unverify action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'verify_user') {
        $userId = intval($_POST['user_id'] ?? 0);
        $status = intval($_POST['status'] ?? 0);
        if ($userId > 0) {
            verifyUserAccount($userId, $status);
            // Redirect to remove POST data
            $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?');
            if (!empty($_GET)) {
                $redirectUrl .= '?' . http_build_query($_GET);
            }
            echo '<script>window.location.href = "' . $redirectUrl . '";</script>';
            exit;
        }
    }
}

// Get filter parameters
$verifiedFilter = $_GET['verified'] ?? '';
$votedFilter = $_GET['voted'] ?? '';
$departmentFilter = $_GET['department'] ?? '';
$searchFilter = $_GET['search'] ?? '';

// Pagination
$limit = 20;
$page = isset($_GET['page_num']) ? max(1, intval($_GET['page_num'])) : 1;
$offset = ($page - 1) * $limit;

// Get total count for pagination
$totalUsers = getVotersCount($verifiedFilter, $votedFilter, $departmentFilter, $searchFilter);
$totalPages = ceil($totalUsers / $limit);

// Get voters with filters and pagination
$users = getVoters($verifiedFilter, $votedFilter, $departmentFilter, $searchFilter, $limit, $offset);

// Custom vote fetching (getUserVotes not auto-loaded)
$activeBatch = 1; // Default to batch 1 as per your query; change if needed
foreach ($users as &$u) {
    $u['votes'] = [];
    $u['vote_count'] = 0;
    if (isset($u['is_voted']) && $u['is_voted']) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT v.*, c.cand_fullname, c.cand_position, c.cand_partylist 
                FROM votes v 
                LEFT JOIN candidate c ON v.cand_id = c.cand_id 
                WHERE v.user_id = ? AND v.election_batch = ?
                ORDER BY v.position
            ");
            $stmt->execute([$u['id'], $activeBatch]);
            $u['votes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $u['vote_count'] = count($u['votes']);
        } catch (PDOException $e) {
            error_log("Error fetching votes for user " . $u['id'] . ": " . $e->getMessage());
        }
    }
}

// Get all departments for filter dropdown
$departments = [];
try {
    $pdo = getDBConnection();
    $departments = $pdo->query("SELECT `dept_name` FROM `department` ORDER BY `dept_name`")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Error fetching departments: " . $e->getMessage());
}
?>

<div class="voters-page">
    <!-- Filters -->
    <div class="voters-filters" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
        <form method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <input type="hidden" name="page" value="votersAccount">
            <input type="hidden" name="page_num" value="<?php echo $page; ?>">
            
            <!-- Search -->
            <div class="filter-group">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchFilter); ?>" placeholder="Search by ID or Name..." style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary); font-size: 13px; width: 200px;">
            </div>
            
            <button type="submit" class="btn-action btn-edit" title="Search">
                <i class="fas fa-search"></i>
            </button>
            
            <div class="filter-group">
                <select name="verified" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary); font-size: 13px;">
                    <option value="">All Verified Status</option>
                    <option value="verified" <?php echo $verifiedFilter === 'verified' ? 'selected' : ''; ?>>Verified</option>
                    <option value="unverified" <?php echo $verifiedFilter === 'unverified' ? 'selected' : ''; ?>>Unverified</option>
                </select>
            </div>
            
            <div class="filter-group">
                <select name="voted" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary); font-size: 13px;">
                    <option value="">All Voting Status</option>
                    <option value="voted" <?php echo $votedFilter === 'voted' ? 'selected' : ''; ?>>Voted</option>
                    <option value="not_voted" <?php echo $votedFilter === 'not_voted' ? 'selected' : ''; ?>>Not Voted</option>
                </select>
            </div>
            
            <div class="filter-group">
                <select name="department" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary); font-size: 13px;">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo $departmentFilter === $dept ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dept); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($verifiedFilter || $votedFilter || $departmentFilter || $searchFilter): ?>
            <a href="main.php?page=votersAccount" class="btn" style="padding: 8px 14px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); color: var(--text-secondary); font-size: 13px; text-decoration: none;">
                <i class="fas fa-times"></i> Clear Filters
            </a>
            <?php endif; ?>
        </form>
    </div>

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
                    <th>Cast Votes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No voters found.</td>
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
                        <td>
                            <?php if ($u['vote_count'] > 0): ?>
                            <button class="btn-action btn-edit vote-toggle" data-user-id="<?php echo $u['id']; ?>" title="View cast votes" style="display: flex; align-items: center; gap: 4px;">
                                <span class="vote-count"><?php echo $u['vote_count']; ?> vote<?php echo $u['vote_count'] > 1 ? 's' : ''; ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <?php else: ?>
                            <span class="status-badge inactive">No votes</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="verify_user">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <input type="hidden" name="status" value="<?php echo $u['is_verified'] ? 0 : 1; ?>">
                                <button type="submit" class="btn-action <?php echo $u['is_verified'] ? 'btn-delete' : 'btn-edit'; ?>" title="<?php echo $u['is_verified'] ? 'Unverify' : 'Verify'; ?>">
                                    <i class="fas <?php echo $u['is_verified'] ? 'fa-times' : 'fa-check'; ?>"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php if ($u['vote_count'] > 0): ?>
                    <tr class="vote-details" data-user-id="<?php echo $u['id']; ?>" style="display: none; background: rgba(0,0,0,0.02);">
                        <td colspan="7">
                            <div class="vote-list">
                                <h4 style="margin: 0 0 10px 0; color: var(--text-primary);">Cast Votes (Batch <?php echo $activeBatch; ?>):</h4>
                                <?php foreach ($u['votes'] as $vote): ?>
                                <div class="vote-item" style="margin-bottom: 8px; padding: 8px; background: var(--bg-card); border-radius: var(--radius); border-left: 3px solid var(--primary); font-size: 13px;">
                                    <strong><?php echo htmlspecialchars($vote['position'] ?? $vote['cand_position'] ?? 'Unknown Position'); ?>:</strong> 
                                    <?php echo htmlspecialchars($vote['cand_fullname'] ?? 'ID: ' . $vote['cand_id']); ?>
                                    <?php if ($vote['cand_partylist']): ?><small> (<?php echo htmlspecialchars($vote['cand_partylist']); ?>)</small><?php endif; ?>
                                    <span style="float: right; font-size: 11px; color: var(--text-muted);">
                                        Vote ID: <?php echo htmlspecialchars($vote['vote_id']); ?> | 
                                        <?php echo $vote['voted_at'] ? date('M j, Y g:i A', strtotime($vote['voted_at'])) : 'N/A'; ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-top: 1px solid var(--border);">
            <span style="font-size: 13px; color: var(--text-muted);">
                Showing <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $totalUsers); ?> of <?php echo $totalUsers; ?> voters
            </span>
            <div style="display: flex; gap: 6px; align-items: center;">
                <?php 
                // Build query string for pagination
                $queryParams = $_GET;
                unset($queryParams['page_num']);
                $baseUrl = '?' . http_build_query($queryParams);
                if (!empty($queryParams)) {
                    $baseUrl .= '&';
                } else {
                    $baseUrl = '?';
                }
                ?>
                
                <?php if ($page > 1): ?>
                <a href="<?php echo $baseUrl; ?>page_num=<?php echo $page - 1; ?>" class="btn-action" style="text-decoration: none;">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php 
                // Show page numbers
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="<?php echo $baseUrl; ?>page_num=<?php echo $i; ?>" 
                   class="btn-action <?php echo $i === $page ? 'btn-edit' : ''; ?>"
                   style="min-width: 32px; text-decoration: none; <?php echo $i === $page ? 'background: var(--text-primary); color: var(--bg-card);' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="<?php echo $baseUrl; ?>page_num=<?php echo $page + 1; ?>" class="btn-action" style="text-decoration: none;">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.vote-toggle').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.dataset.userId;
            const detailsRow = document.querySelector('.vote-details[data-user-id="' + userId + '"]');
            if (detailsRow) {
                detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        });
    });
});
</script>
