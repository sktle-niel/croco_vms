<?php
/**
 * Voters Account Page - Cleaned Version (Actions Removed)
 * Displays filtered, paginated list of voters
 */
require_once __DIR__ . '/../../backend/include.php';

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
            <input type="hidden" name="page_num" value="<?php echo $page; ?>">
            
            <!-- Search -->
            <div class="filter-group">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchFilter); ?>" placeholder="Search by ID or Name..." style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary); font-size: 13px; width: 200px;">
            </div>
            
            <button type="submit" class="btn-action btn-edit" title="Search">
                <i class="fas fa-search"></i>
            </button>
            
            <!-- Verified Filter -->
            <div class="filter-group">
                <select name="verified" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary); font-size: 13px;">
                    <option value="">All Verified Status</option>
                    <option value="verified" <?php echo $verifiedFilter === 'verified' ? 'selected' : ''; ?>>Verified</option>
                    <option value="unverified" <?php echo $verifiedFilter === 'unverified' ? 'selected' : ''; ?>>Unverified</option>
                </select>
            </div>
            
            <!-- Voted Filter -->
            <div class="filter-group">
                <select name="voted" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary); font-size: 13px;">
                    <option value="">All Voting Status</option>
                    <option value="voted" <?php echo $votedFilter === 'voted' ? 'selected' : ''; ?>>Voted</option>
                    <option value="not_voted" <?php echo $votedFilter === 'not_voted' ? 'selected' : ''; ?>>Not Voted</option>
                </select>
            </div>
            
            <!-- Department Filter -->
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
            
            <!-- Clear Filters -->
            <?php if ($verifiedFilter || $votedFilter || $departmentFilter || $searchFilter): ?>
            <a href="main.php?page=votersAccount" class="btn" style="padding: 8px 14px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); color: var(--text-secondary); font-size: 13px; text-decoration: none;">
                <i class="fas fa-times"></i> Clear
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Data Table -->
    <div class="data-table-container">
        <div class="table-header">
            <h2>Voters Account List</h2>
            <span class="count"><?php echo number_format($totalUsers); ?> total voters</span>
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
                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        No voters found matching your filters.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($u['school_id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['department'] ?? 'N/A'); ?></td>
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

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination-container">
            <?php 
            $queryParams = $_GET;
            unset($queryParams['page_num']);
            $baseQuery = http_build_query($queryParams);
            $baseUrl = $baseQuery ? '?' . $baseQuery . '&page_num=' : '?page_num=';
            ?>
            <div class="pagination-info">
                Showing <?php echo min($offset + count($users), $totalUsers); ?> of <?php echo number_format($totalUsers); ?> voters
            </div>
            <div class="pagination-nav">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo $baseUrl . ($page - 1); ?>" class="page-link" title="Previous">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                for ($i = $startPage; $i <= $endPage; $i++): 
                ?>
                    <a href="?<?php echo $baseUrl . $i; ?>" 
                       class="page-link <?php echo $i === $page ? 'active' : ''; ?>"
                       title="Page <?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?<?php echo $baseUrl . ($page + 1); ?>" class="page-link" title="Next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Minimal inline styles for table improvements */
.data-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--bg-card);
}

.data-table th {
    background: var(--bg-secondary);
    font-weight: 600;
    padding: 12px 16px;
    text-align: left;
    border-bottom: 2px solid var(--border);
}

.data-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-top: 1px solid var(--border);
    background: var(--bg-card);
}

.pagination-info {
    font-size: 13px;
    color: var(--text-muted);
}

.pagination-nav {
    display: flex;
    gap: 4px;
}

.page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 8px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: var(--bg-secondary);
    color: var(--text-primary);
    text-decoration: none;
    font-size: 13px;
    transition: all 0.2s;
}

.page-link:hover {
    background: var(--text-primary);
    color: var(--bg-card);
}

.page-link.active {
    background: var(--text-primary);
    color: var(--bg-card);
    border-color: var(--text-primary);
}
</style>
