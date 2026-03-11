<?php
require_once __DIR__ . '/../../backend/include.php';
require_once __DIR__ . '/../../administrator/components/successMessage.php';
require_once __DIR__ . '/../../administrator/components/invalidMessage.php';

$setupData = processSetupPage();
$message = $setupData['message'];
$messageType = $setupData['messageType'];
$partylists = $setupData['partylists'];
$batches = $setupData['batches'];
$departments = $setupData['departments'];

renderAdminSuccessOverlay();
renderAdminInvalidOverlay();
?>
<!-- Manage Voting Page -->

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
                <input type="text" name="partylist_name" placeholder="e.g. IIC2 TEAM" required>
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
                                >
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
                <input type="text" name="elc_name" placeholder="Election name e.g. IC2 ELECTION" required>
                <input type="text" name="elc_schoolyear" placeholder="School year e.g. 2025-2026" required>
                <input type="text" name="elc_createdby" placeholder="Created by e.g. Admin">
                <select name="elc_status">
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
                                >
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
                <input type="text" name="dept_name" placeholder="e.g. BSIT" required>
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
                                >
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

<?php if ($message): ?>
<script>
    (function waitForToast() {
        <?php if ($messageType === 'success'): ?>
        if (typeof showAdminSuccess === 'function') {
            showAdminSuccess('<?php echo addslashes($message); ?>');
        } else {
            setTimeout(waitForToast, 50);
        }
        <?php else: ?>
        if (typeof showAdminError === 'function') {
            showAdminError('<?php echo addslashes($message); ?>');
        } else {
            setTimeout(waitForToast, 50);
        }
        <?php endif; ?>
    })();
</script>
<?php endif; ?>