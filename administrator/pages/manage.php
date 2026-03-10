<!-- Manage Candidates Page -->
<?php
require_once __DIR__ . '/../../backend/include.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $fullname      = trim($_POST['fullname'] ?? '');
        $partylist     = trim($_POST['partylist'] ?? '');
        $position      = trim($_POST['position'] ?? '');
        $electionbatch = trim($_POST['electionbatch'] ?? '');

        $photoPath = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir  = __DIR__ . '/../../img/candidatesImg/';
            $fileName   = time() . '_' . basename($_FILES['photo']['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $photoPath = 'img/candidatesImg/' . $fileName;
            }
        }

        if (!empty($fullname) && !empty($position)) {
            try {
                $pdo  = getDBConnection();
                $stmt = $pdo->prepare("INSERT INTO candidate (cand_fullname, cand_partylist, cand_position, cand_photo, cand_electionbatch) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$fullname, $partylist, $position, $photoPath, $electionbatch]);
                $message     = 'Candidate added successfully!';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message     = 'Error adding candidate: ' . $e->getMessage();
                $messageType = 'error';
            }
        } else {
            $message     = 'Please fill in required fields.';
            $messageType = 'error';
        }
    }

    if ($_POST['action'] === 'delete') {
        $candId = intval($_POST['cand_id'] ?? 0);
        if ($candId > 0) {
            try {
                $pdo  = getDBConnection();
                $stmt = $pdo->prepare("DELETE FROM candidate WHERE cand_id = ?");
                $stmt->execute([$candId]);
                $message     = 'Candidate removed successfully!';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message     = 'Error deleting candidate: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
}

$candidates = [];
try {
    $pdo  = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM candidate ORDER BY FIELD(cand_position, 'President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor', 'P.R.O', 'Senator'), cand_fullname");
    $candidates = $stmt->fetchAll();
} catch (PDOException $e) {
    $message     = 'Error fetching candidates: ' . $e->getMessage();
    $messageType = 'error';
}
?>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
    <?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<div class="manage-page">

    <!-- Add Candidate Panel -->
    <div class="add-panel">
        <div class="add-panel-header">
            <h2>Add Candidate</h2>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">

            <div class="add-panel-body">

                <div class="field-group">
                    <label>Full Name <span class="required-star">*</span></label>
                    <input type="text" name="fullname" placeholder="e.g. Niel Patrick L. Penlas" required>
                </div>

                <div class="field-group">
                    <label>Position <span class="required-star">*</span></label>
                    <select name="position" required>
                        <option value="">Select Position</option>
                        <option value="President">President</option>
                        <option value="Vice President">Vice President</option>
                        <option value="Secretary">Secretary</option>
                        <option value="Treasurer">Treasurer</option>
                        <option value="Auditor">Auditor</option>
                        <option value="P.R.O">P.R.O</option>
                        <option value="Senator">Senator</option>
                    </select>
                </div>

                <div class="field-group">
                    <label>Partylist</label>
                    <input type="text" name="partylist" placeholder="e.g. Ic2 Team">
                </div>

                <div class="field-group">
                    <label>Election Batch</label>
                    <input type="text" name="electionbatch" placeholder="e.g. 2026">
                </div>

                <div class="field-group">
                    <label>Photo</label>
                    <input type="file" name="photo" accept="image/*" id="photoInput" onchange="previewPhoto(this)">
                    <div class="photo-preview" id="photoPreview">
                        <img id="previewImg" src="" alt="Preview">
                        <div class="preview-placeholder" id="previewPlaceholder">
                            <i class="fas fa-image"></i>
                            <span>No photo selected</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="add-panel-footer">
                <button type="submit" class="btn-submit-candidate">
                    <i class="fas fa-plus"></i>
                    Add Candidate
                </button>
            </div>
        </form>
    </div>

    <!-- Candidates List Panel -->
    <div class="candidates-panel">
        <div class="candidates-panel-header">
            <h2>Candidates</h2>
            <span class="candidates-count"><?php echo count($candidates); ?> total</span>
        </div>

        <div class="candidates-table-container">
            <?php if (empty($candidates)): ?>
            <div class="empty-state">
                <i class="fas fa-user-tie"></i>
                <p>No candidates yet. Add one using the form.</p>
            </div>
            <?php else: ?>
            <table class="candidates-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Partylist</th>
                        <th>Batch</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $c): ?>
                    <tr>
                        <td>
                            <?php if (!empty($c['cand_photo'])): ?>
                                <img src="../../<?php echo htmlspecialchars($c['cand_photo']); ?>"
                                     alt="<?php echo htmlspecialchars($c['cand_fullname']); ?>"
                                     class="cand-photo-cell">
                            <?php else: ?>
                                <div class="cand-photo-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="cand-name"><?php echo htmlspecialchars($c['cand_fullname']); ?></div>
                            <?php if (!empty($c['cand_partylist'])): ?>
                            <div class="cand-partylist"><?php echo htmlspecialchars($c['cand_partylist']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="position-tag"><?php echo htmlspecialchars($c['cand_position']); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($c['cand_partylist'] ?? '—'); ?></td>
                        <td>
                            <span class="batch-text"><?php echo htmlspecialchars($c['cand_electionbatch'] ?? '—'); ?></span>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="cand_id" value="<?php echo $c['cand_id']; ?>">
                                <button type="submit" class="btn-remove"
                                    onclick="return confirm('Remove <?php echo htmlspecialchars($c['cand_fullname']); ?>?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
function previewPhoto(input) {
    const img         = document.getElementById('previewImg');
    const placeholder = document.getElementById('previewPlaceholder');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            img.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        img.style.display = 'none';
        placeholder.style.display = 'flex';
    }
}
</script>