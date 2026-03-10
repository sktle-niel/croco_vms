<!-- Manage Voting Page -->
<?php
require_once __DIR__ . '/../../backend/include.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // ── Partylist ──
    if ($_POST['action'] === 'add_partylist') {
        $name = trim($_POST['partylist_name'] ?? '');
        if (!empty($name)) {
            try {
                $pdo  = getDBConnection();
                $stmt = $pdo->prepare("INSERT INTO partylist (partylist_name) VALUES (?)");
                $stmt->execute([$name]);
                $message     = 'Partylist added successfully!';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message     = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        } else {
            $message     = 'Partylist name is required.';
            $messageType = 'error';
        }
    }

    if ($_POST['action'] === 'delete_partylist') {
        $id = intval($_POST['partylist_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo  = getDBConnection();
                $stmt = $pdo->prepare("DELETE FROM partylist WHERE partylist_id = ?");
                $stmt->execute([$id]);
                $message     = 'Partylist removed.';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message     = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }

    // ── Election Batch ──
    if ($_POST['action'] === 'add_batch') {
        $elc_name       = trim($_POST['elc_name'] ?? '');
        $elc_schoolyear = trim($_POST['elc_schoolyear'] ?? '');
        $elc_status     = trim($_POST['elc_status'] ?? 'active');
        $elc_createdby  = trim($_POST['elc_createdby'] ?? '');

        if (!empty($elc_name) && !empty($elc_schoolyear)) {
            try {
                $pdo  = getDBConnection();
                $stmt = $pdo->prepare("INSERT INTO election_batch (elc_name, elc_schoolyear, elc_status, elc_createdby) VALUES (?, ?, ?, ?)");
                $stmt->execute([$elc_name, $elc_schoolyear, $elc_status, $elc_createdby]);
                $message     = 'Election batch added successfully!';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message     = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        } else {
            $message     = 'Election name and school year are required.';
            $messageType = 'error';
        }
    }

    if ($_POST['action'] === 'delete_batch') {
        $id = intval($_POST['batch_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo  = getDBConnection();
                $stmt = $pdo->prepare("DELETE FROM election_batch WHERE elc_id = ?");
                $stmt->execute([$id]);
                $message     = 'Election batch removed.';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message     = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }

    // ── Department ──
    if ($_POST['action'] === 'add_department') {
        $dept = trim($_POST['dept_name'] ?? '');
        if (!empty($dept)) {
            try {
                $pdo  = getDBConnection();
                $stmt = $pdo->prepare("INSERT INTO department (dept_name) VALUES (?)");
                $stmt->execute([$dept]);
                $message     = 'Department added successfully!';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message     = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        } else {
            $message     = 'Department name is required.';
            $messageType = 'error';
        }
    }

    if ($_POST['action'] === 'delete_department') {
        $id = intval($_POST['dept_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo  = getDBConnection();
                $stmt = $pdo->prepare("DELETE FROM department WHERE dept_id = ?");
                $stmt->execute([$id]);
                $message     = 'Department removed.';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message     = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
}

// ── Fetch all ──
$partylists  = [];
$batches     = [];
$departments = [];

try {
    $pdo         = getDBConnection();
    $partylists  = $pdo->query("SELECT * FROM partylist ORDER BY partylist_name")->fetchAll();
    $batches     = $pdo->query("SELECT * FROM election_batch ORDER BY elc_schoolyear DESC")->fetchAll();
    $departments = $pdo->query("SELECT * FROM department ORDER BY dept_name")->fetchAll();
} catch (PDOException $e) {
    $message     = 'Error fetching data: ' . $e->getMessage();
    $messageType = 'error';
}
?>

<style>
.voting-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.voting-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    align-items: start;
}

/* ── Panel ── */
.voting-panel {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}

.voting-panel-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.voting-panel-header h2 {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-primary);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.voting-panel-header h2 i {
    font-size: 12px;
    color: var(--text-muted);
}

.item-count {
    font-size: 11px;
    font-family: 'DM Mono', monospace;
    font-weight: 600;
    color: var(--text-muted);
    background: var(--bg);
    border: 1px solid var(--border);
    padding: 2px 8px;
    border-radius: 20px;
}

/* ── Add Form ── */
.voting-panel-form {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    display: flex;
    gap: 8px;
}

.voting-panel-form input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 13px;
    background: var(--bg);
    color: var(--text-primary);
    font-family: 'DM Sans', sans-serif;
    outline: none;
    transition: border-color var(--transition);
    min-width: 0;
}

.voting-panel-form input:focus {
    border-color: var(--text-primary);
}

.btn-add-item {
    padding: 8px 14px;
    background: var(--text-primary);
    color: var(--bg-card);
    border: none;
    border-radius: var(--radius);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    transition: opacity var(--transition);
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
}

.btn-add-item:hover { opacity: 0.8; }

/* ── Items List ── */
.voting-panel-list {
    max-height: 320px;
    overflow-y: auto;
}

.voting-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 18px;
    border-bottom: 1px solid var(--border);
    transition: background var(--transition);
}

.voting-item:last-child { border-bottom: none; }
.voting-item:hover { background: var(--bg); }

.voting-item-label {
    font-size: 13px;
    color: var(--text-primary);
    font-weight: 500;
}

.voting-item-meta {
    font-size: 11px;
    color: var(--text-muted);
    font-family: 'DM Mono', monospace;
    margin-top: 1px;
}

.btn-remove-item {
    width: 26px;
    height: 26px;
    border: 1px solid var(--border);
    background: var(--bg-card);
    border-radius: var(--radius);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: var(--text-muted);
    transition: all var(--transition);
    flex-shrink: 0;
}

.btn-remove-item:hover {
    border-color: var(--danger-color);
    color: var(--danger-color);
    background: #fff1f1;
}

[data-theme="dark"] .btn-remove-item:hover {
    background: #2a0d0d;
}

/* ── Empty ── */
.voting-empty {
    padding: 32px 18px;
    text-align: center;
    color: var(--text-muted);
    font-size: 12px;
}

.voting-empty i {
    display: block;
    font-size: 22px;
    opacity: 0.25;
    margin-bottom: 8px;
}

/* ── Alert ── */
.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-radius: var(--radius);
    font-size: 13px;
    font-weight: 500;
    border: 1px solid;
}

.alert-success {
    background: #f0fff4;
    color: var(--success-color);
    border-color: #c3e6cb;
}

.alert-error {
    background: #fff1f1;
    color: var(--danger-color);
    border-color: #f5c6cb;
}

[data-theme="dark"] .alert-success { background: #0d2a1a; border-color: #1a4a2a; }
[data-theme="dark"] .alert-error   { background: #2a0d0d; border-color: #4a1a1a; }

@media (max-width: 900px) {
    .voting-grid { grid-template-columns: 1fr; }
}
</style>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
    <?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<div class="voting-page">
    <div class="voting-grid">

        <!-- ── Partylist Panel ── -->
        <div class="voting-panel">
            <div class="voting-panel-header">
                <h2><i class="fas fa-flag"></i> Partylist</h2>
                <span class="item-count"><?php echo count($partylists); ?></span>
            </div>

            <form method="POST" class="voting-panel-form">
                <input type="hidden" name="action" value="add_partylist">
                <input type="text" name="partylist_name" placeholder="e.g. Uniteam" required>
                <button type="submit" class="btn-add-item">
                    <i class="fas fa-plus"></i> Add
                </button>
            </form>

            <div class="voting-panel-list">
                <?php if (empty($partylists)): ?>
                <div class="voting-empty">
                    <i class="fas fa-flag"></i>
                    No partylists added yet.
                </div>
                <?php else: ?>
                    <?php foreach ($partylists as $p): ?>
                    <div class="voting-item">
                        <div>
                            <div class="voting-item-label"><?php echo htmlspecialchars($p['partylist_name']); ?></div>
                            <div class="voting-item-meta">#<?php echo $p['partylist_id']; ?></div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="delete_partylist">
                            <input type="hidden" name="partylist_id" value="<?php echo $p['partylist_id']; ?>">
                            <button type="submit" class="btn-remove-item"
                                onclick="return confirm('Remove <?php echo htmlspecialchars($p['partylist_name']); ?>?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Election Batch Panel ── -->
        <div class="voting-panel">
            <div class="voting-panel-header">
                <h2><i class="fas fa-calendar-alt"></i> Election Batch</h2>
                <span class="item-count"><?php echo count($batches); ?></span>
            </div>

            <form method="POST" class="voting-panel-form" style="flex-direction: column; gap: 8px;">
                <input type="hidden" name="action" value="add_batch">
                <input type="text" name="elc_name" placeholder="Election name e.g. SSG Election" required>
                <input type="text" name="elc_schoolyear" placeholder="School year e.g. 2025-2026" required>
                <input type="text" name="elc_createdby" placeholder="Created by e.g. Admin">
                <select name="elc_status" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 13px; background: var(--bg); color: var(--text-primary); font-family: 'DM Sans', sans-serif; outline: none;">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button type="submit" class="btn-add-item" style="width: 100%; justify-content: center;">
                    <i class="fas fa-plus"></i> Add Batch
                </button>
            </form>

            <div class="voting-panel-list">
                <?php if (empty($batches)): ?>
                <div class="voting-empty">
                    <i class="fas fa-calendar-alt"></i>
                    No election batches added yet.
                </div>
                <?php else: ?>
                    <?php foreach ($batches as $b): ?>
                    <div class="voting-item">
                        <div>
                            <div class="voting-item-label"><?php echo htmlspecialchars($b['elc_name']); ?></div>
                            <div class="voting-item-meta">
                                <?php echo htmlspecialchars($b['elc_schoolyear']); ?>
                                &nbsp;·&nbsp;
                                <span style="color: <?php echo $b['elc_status'] === 'active' ? 'var(--success-color)' : 'var(--text-muted)'; ?>">
                                    <?php echo ucfirst($b['elc_status']); ?>
                                </span>
                            </div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="delete_batch">
                            <input type="hidden" name="batch_id" value="<?php echo $b['elc_id']; ?>">
                            <button type="submit" class="btn-remove-item"
                                onclick="return confirm('Remove batch <?php echo htmlspecialchars($b['elc_name']); ?>?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Department Panel ── -->
        <div class="voting-panel">
            <div class="voting-panel-header">
                <h2><i class="fas fa-building"></i> Department</h2>
                <span class="item-count"><?php echo count($departments); ?></span>
            </div>

            <form method="POST" class="voting-panel-form">
                <input type="hidden" name="action" value="add_department">
                <input type="text" name="dept_name" placeholder="e.g. BSICT" required>
                <button type="submit" class="btn-add-item">
                    <i class="fas fa-plus"></i> Add
                </button>
            </form>

            <div class="voting-panel-list">
                <?php if (empty($departments)): ?>
                <div class="voting-empty">
                    <i class="fas fa-building"></i>
                    No departments added yet.
                </div>
                <?php else: ?>
                    <?php foreach ($departments as $d): ?>
                    <div class="voting-item">
                        <div>
                            <div class="voting-item-label"><?php echo htmlspecialchars($d['dept_name']); ?></div>
                            <div class="voting-item-meta">#<?php echo $d['dept_id']; ?></div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="delete_department">
                            <input type="hidden" name="dept_id" value="<?php echo $d['dept_id']; ?>">
                            <button type="submit" class="btn-remove-item"
                                onclick="return confirm('Remove <?php echo htmlspecialchars($d['dept_name']); ?>?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>