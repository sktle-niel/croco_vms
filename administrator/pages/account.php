<!-- Account Management Page -->
<?php
ob_start();
require_once __DIR__ . '/../../backend/include.php';

$accountData = processAccountPage();
$message = $accountData['message'];
$messageType = $accountData['messageType'];
$accounts = $accountData['accounts'];
?>

<style>
.account-page {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 20px;
    align-items: start;
}

/* ── Form Panel ── */
.account-form-panel {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    position: sticky;
    top: calc(var(--header-height) + 24px);
}

.account-form-panel .panel-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
}

.account-form-panel .panel-header h2 {
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

.account-form-panel .panel-header h2 i {
    font-size: 12px;
    color: var(--text-muted);
}

.panel-body {
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.field-group label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.field-group input {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 13px;
    background: var(--bg);
    color: var(--text-primary);
    font-family: 'DM Sans', sans-serif;
    outline: none;
    transition: border-color var(--transition);
}

.field-group input:focus {
    border-color: var(--text-primary);
}

.field-hint {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 2px;
}

.required-star {
    color: var(--danger-color);
    margin-left: 2px;
}

.user-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    letter-spacing: 0.04em;
}

.user-type-badge i {
    font-size: 11px;
    color: var(--text-muted);
}

.panel-footer {
    padding: 14px 18px;
    border-top: 1px solid var(--border);
}

.btn-create {
    width: 100%;
    padding: 10px;
    background: var(--text-primary);
    color: var(--bg-card);
    border: none;
    border-radius: var(--radius);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    letter-spacing: 0.02em;
    transition: opacity var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
}

.btn-create:hover { opacity: 0.8; }

/* ── Accounts Panel ── */
.accounts-panel {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.accounts-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.accounts-panel-header h2 {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin: 0;
}

.accounts-count {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    font-family: 'DM Mono', monospace;
    background: var(--bg-card);
    border: 1px solid var(--border);
    padding: 3px 10px;
    border-radius: 20px;
}

/* ── Table ── */
.accounts-table-container {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}

.accounts-table {
    width: 100%;
    border-collapse: collapse;
}

.accounts-table thead th {
    padding: 10px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
}

.accounts-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background var(--transition);
}

.accounts-table tbody tr:last-child { border-bottom: none; }
.accounts-table tbody tr:hover { background: var(--bg); }

.accounts-table tbody td {
    padding: 12px 16px;
    font-size: 13px;
    color: var(--text-primary);
    vertical-align: middle;
}

.account-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--bg);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: var(--text-muted);
    flex-shrink: 0;
}

.account-email {
    font-weight: 500;
    font-size: 13px;
    color: var(--text-primary);
}

.account-id {
    font-size: 11px;
    color: var(--text-muted);
    font-family: 'DM Mono', monospace;
    margin-top: 2px;
}

.role-tag {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.03em;
    background: var(--bg);
    border: 1px solid var(--border);
    color: var(--text-secondary);
    text-transform: uppercase;
}

.date-text {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    color: var(--text-muted);
}

/* ── Action Buttons ── */
.td-actions {
    display: flex;
    gap: 6px;
    align-items: center;
}

.btn-icon {
    width: 28px;
    height: 28px;
    border: 1px solid var(--border);
    background: var(--bg-card);
    border-radius: var(--radius);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: var(--text-muted);
    transition: all var(--transition);
}

.btn-icon.btn-reset:hover {
    border-color: var(--text-primary);
    color: var(--text-primary);
    background: var(--bg);
}

.btn-icon.btn-delete:hover {
    border-color: var(--danger-color);
    color: var(--danger-color);
    background: #fff1f1;
}

[data-theme="dark"] .btn-icon.btn-delete:hover { background: #2a0d0d; }

/* ── Empty State ── */
.empty-state {
    padding: 48px 20px;
    text-align: center;
    color: var(--text-muted);
}

.empty-state i {
    display: block;
    font-size: 28px;
    opacity: 0.25;
    margin-bottom: 10px;
}

.empty-state p {
    font-size: 13px;
    margin: 0;
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
    margin-bottom: 16px;
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

/* ── Reset Password Modal ── */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}

.modal-overlay.active { display: flex; }

.modal-box {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    width: 90%;
    max-width: 380px;
    overflow: hidden;
    animation: modalIn 0.18s ease;
}

@keyframes modalIn {
    from { opacity: 0; transform: translateY(-12px); }
    to   { opacity: 1; transform: translateY(0); }
}

.modal-box-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-box-header h3 {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-primary);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin: 0;
}

.modal-close {
    width: 26px;
    height: 26px;
    border: 1px solid var(--border);
    background: var(--bg-card);
    border-radius: var(--radius);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: var(--text-muted);
    transition: all var(--transition);
}

.modal-close:hover {
    border-color: var(--danger-color);
    color: var(--danger-color);
}

.modal-box-body {
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.modal-email-display {
    padding: 9px 12px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 12px;
    color: var(--text-secondary);
    font-family: 'DM Mono', monospace;
}

.modal-box-footer {
    padding: 14px 18px;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.btn-cancel {
    padding: 8px 16px;
    background: var(--bg);
    color: var(--text-secondary);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    transition: all var(--transition);
}

.btn-cancel:hover { border-color: var(--text-primary); color: var(--text-primary); }

.btn-confirm-reset {
    padding: 8px 16px;
    background: var(--text-primary);
    color: var(--bg-card);
    border: none;
    border-radius: var(--radius);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    transition: opacity var(--transition);
}

.btn-confirm-reset:hover { opacity: 0.8; }

@media (max-width: 1024px) {
    .account-page { grid-template-columns: 1fr; }
    .account-form-panel { position: static; }
}
</style>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
    <?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<div class="account-page">

    <!-- ── Create Account Panel ── -->
    <div class="account-form-panel">
        <div class="panel-header">
            <h2><i class="fas fa-user-plus"></i> Create Stelcom Account</h2>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="create_account">

            <div class="panel-body">

                <div class="field-group">
                    <label>User Type</label>
                    <div class="user-type-badge">
                        <i class="fas fa-shield-alt"></i>
                        Stelcom
                    </div>
                </div>

                <div class="field-group">
                    <label>Email Address <span class="required-star">*</span></label>
                    <input type="email" name="email" placeholder="e.g. stelcom@ptci.edu.ph" required>
                </div>

                <div class="field-group">
                    <label>Password <span class="required-star">*</span></label>
                    <input type="password" name="password" placeholder="Min. 8 characters" required>
                    <span class="field-hint">At least 8 characters.</span>
                </div>

                <div class="field-group">
                    <label>Confirm Password <span class="required-star">*</span></label>
                    <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                </div>

            </div>

            <div class="panel-footer">
                <button type="submit" class="btn-create">
                    <i class="fas fa-plus"></i>
                    Create Account
                </button>
            </div>
        </form>
    </div>

    <!-- ── Accounts List Panel ── -->
    <div class="accounts-panel">
        <div class="accounts-panel-header">
            <h2>Stelcom Accounts</h2>
            <span class="accounts-count"><?php echo count($accounts); ?> total</span>
        </div>

        <div class="accounts-table-container">
            <?php if (empty($accounts)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No stelcom accounts yet. Create one using the form.</p>
            </div>
            <?php else: ?>
            <table class="accounts-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $acc): ?>
                    <tr>
                        <td>
                            <div class="account-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                        </td>
                        <td>
                            <div class="account-email"><?php echo htmlspecialchars($acc['email']); ?></div>
                            <div class="account-id"><?php echo htmlspecialchars($acc['id']); ?></div>
                        </td>
                        <td>
                            <span class="role-tag"><?php echo htmlspecialchars($acc['user_type']); ?></span>
                        </td>
                        <td>
                            <span class="date-text">
                                <?php echo date('M d, Y', strtotime($acc['created_at'])); ?>
                            </span>
                        </td>
                        <td>
                            <div class="td-actions">
                                <!-- Reset Password -->
                                <button type="button" class="btn-icon btn-reset"
                                    title="Reset Password"
                                    onclick="openResetModal('<?php echo $acc['id']; ?>', '<?php echo htmlspecialchars($acc['email']); ?>')">
                                    <i class="fas fa-key"></i>
                                </button>

                                <!-- Delete -->
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="action" value="delete_account">
                                    <input type="hidden" name="account_id" value="<?php echo $acc['id']; ?>">
                                    <button type="submit" class="btn-icon btn-delete"
                                        title="Delete Account"
                                        onclick="return confirm('Delete account <?php echo htmlspecialchars($acc['email']); ?>? This cannot be undone.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ── Reset Password Modal ── -->
<div class="modal-overlay" id="resetModal">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3><i class="fas fa-key"></i> &nbsp;Reset Password</h3>
            <button class="modal-close" onclick="closeResetModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="account_id" id="resetAccountId">

            <div class="modal-box-body">
                <div class="field-group">
                    <label>Account</label>
                    <div class="modal-email-display" id="resetEmailDisplay">—</div>
                </div>
                <div class="field-group">
                    <label>New Password <span class="required-star">*</span></label>
                    <input type="password" name="new_password" placeholder="Min. 8 characters" required>
                </div>
            </div>

            <div class="modal-box-footer">
                <button type="button" class="btn-cancel" onclick="closeResetModal()">Cancel</button>
                <button type="submit" class="btn-confirm-reset">
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openResetModal(id, email) {
    document.getElementById('resetAccountId').value  = id;
    document.getElementById('resetEmailDisplay').textContent = email;
    document.getElementById('resetModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeResetModal() {
    document.getElementById('resetModal').classList.remove('active');
    document.body.style.overflow = '';
}

document.getElementById('resetModal').addEventListener('click', function(e) {
    if (e.target === this) closeResetModal();
});
</script>
<?php
ob_end_flush();
?>
