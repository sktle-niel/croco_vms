<!-- Account Management Page -->
<?php
ob_start();
require_once __DIR__ . '/../../backend/include.php';

$accountData = processAccountPage();
$message = $accountData['message'];
$messageType = $accountData['messageType'];
$accounts = $accountData['accounts'];
?>

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
                    <label>User Type <span class="required-star">*</span></label>
                    <select name="user_type" required style="padding: 9px 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg); color: var(--text-primary); font-size: 13px; font-family: 'DM Sans', sans-serif;">
                        <option value="">Select user type</option>
                        <option value="stelcom">Stelcom</option>
                        <option value="moderator">Moderator</option>
                    </select>
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
    <div class="accounts-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">


        <div class="accounts-panel-header">
            <h2>Moderator Accounts</h2>
            <span class="accounts-count"><?php echo count($accounts); ?> total</span>
        </div>
        
        <!-- Moderator Accounts Section -->
        <?php 
        $moderator_accounts = $pdo->query("SELECT id, email, user_type, created_at FROM admins WHERE user_type = 'moderator' ORDER BY created_at DESC")->fetchAll(); 
        ?>
        <div class="accounts-panel-header">
            <h2>Stelcom Accounts</h2>
            <span class="accounts-count"><?php echo count($moderator_accounts); ?> total</span>
        </div>
        
        <div class="accounts-table-container">
            <?php if (empty($moderator_accounts)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No moderator accounts yet. Create one using the form.</p>
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
                    <?php foreach ($moderator_accounts as $acc): ?>
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
                                <button type="button" class="btn-icon btn-reset"
                                    title="Reset Password"
                                    onclick="openResetModal('<?php echo $acc['id']; ?>', '<?php echo htmlspecialchars($acc['email']); ?>')">
                                    <i class="fas fa-key"></i>
                                </button>
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
                                        title="Delete Account">
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
