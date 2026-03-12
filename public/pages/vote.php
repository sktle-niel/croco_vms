<?php
// Prevent browser caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once '../../auth/session.php';
require_once '../../backend/create/fetchCandidate.php';
require_once '../../backend/cast/cast.php';
require_once '../../backend/terminate/terminate.php';
require_once '../../components/successMessage.php';
require_once '../../components/invalidMessage.php';

$userHasVoted = false;
if (isset($_SESSION['user_id'])) {
    $userHasVoted = hasUserVoted($_SESSION['user_id'], getActiveElectionBatch());
}

$candidates = getCandidates();

$candidatesByPosition = [];
foreach ($candidates as $candidate) {
    $position = $candidate['cand_position'];
    if (!isset($candidatesByPosition[$position])) {
        $candidatesByPosition[$position] = [];
    }
    $candidatesByPosition[$position][] = $candidate;
}

$positionOrder = ['President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor', 'PRO', 'Senator', 'Representative'];

$displayPositions = array_filter($positionOrder, function($pos) use ($candidatesByPosition) {
    return isset($candidatesByPosition[$pos]);
});

if (empty($displayPositions)) {
    $displayPositions = array_keys($candidatesByPosition);
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>PTCI Student Council Election</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/css/global.css" />
    <link rel="stylesheet" href="../../assets/css/navbar.css" />
    <link rel="stylesheet" href="../../assets/css/theme.css" />
    <link rel="stylesheet" href="../../assets/css/vote.css" />
    <link rel="stylesheet" href="../../assets/css/status.css" />
    <link rel="stylesheet" href="../../assets/css/confirm.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../../img/logo/PTCI-logo.png" />
  </head>

  <body>
    <div class="floating-logos-bg" id="floatingLogos"></div>

    <?php include '../../components/navigationbar.php'; ?>

    <div class="container-main mt-5">
      <div class="header">
        <h1>Cast Your Vote</h1>
        <p>Select your candidates for each position</p>

        <div class="instructions-box">
          <div class="instructions-title"><i class="fas fa-info-circle"></i> Instructions</div>
          <ul class="instructions-list">
            <li>Select your preferred candidate for each position by clicking on their card</li>
            <li>Review your selections in the panel on the right before submitting</li>
            <li>Once submitted, changes cannot be made. Ensure your choices are final</li>
          </ul>
        </div>
      </div><!-- /.header -->

      <div class="content-wrapper">

        <!-- LEFT: Candidates -->
        <div class="candidates-side">

          <?php if (empty($candidates)): ?>
            <div class="section">
              <div class="position-title">No Candidates Available</div>
              <p style="text-align: center; padding: 20px;">There are no candidates available for this election.</p>
            </div>
          <?php else: ?>
            <?php foreach ($displayPositions as $position): ?>
              <?php
                $positionCandidates = $candidatesByPosition[$position];
                $positionKey = strtolower(str_replace(' ', '', $position));
              ?>
              <div class="section">
                <div class="position-title"><?php echo htmlspecialchars($position); ?></div>

                <?php foreach ($positionCandidates as $index => $candidate): ?>
                  <?php
                    $cardId    = $positionKey . '_' . ($index + 1);
                    $photoPath = !empty($candidate['cand_photo'])
                                   ? '../../' . $candidate['cand_photo']
                                   : '../../img/logo/PTCI-logo.png';
                  ?>
                  <div class="candidate-card" id="<?php echo $cardId; ?>"
                       onclick="selectCandidate(
                         '<?php echo $cardId; ?>',
                         '<?php echo htmlspecialchars($candidate['cand_fullname']); ?>',
                         '<?php echo $positionKey; ?>',
                         <?php echo $candidate['cand_id']; ?>
                       )">
                    <img class="avatar"
                         src="<?php echo htmlspecialchars($photoPath); ?>"
                         onerror="this.src='../../img/logo/PTCI-logo.png'"
                         alt="Candidate">
                    <div class="info">
                      <div class="name"><?php echo htmlspecialchars($candidate['cand_fullname']); ?></div>
                      <div class="dept"><?php echo htmlspecialchars($candidate['cand_partylist']); ?></div>
                      <div class="desc"><?php echo htmlspecialchars($candidate['cand_position']); ?></div>
                    </div><!-- /.info -->
                    <div class="check"><i class="fas fa-check"></i></div>
                  </div><!-- /.candidate-card -->
                <?php endforeach; ?>

              </div><!-- /.section -->
            <?php endforeach; ?>
          <?php endif; ?>

        </div><!-- /.candidates-side -->

        <!-- RIGHT: Selection Panel -->
        <div class="selection-panel">
          <h3>Your Selection</h3>

          <?php foreach ($displayPositions as $position): ?>
            <?php $posKey = strtolower(str_replace(' ', '', $position)); ?>
            <div class="selection-item">
              <div class="label"><?php echo htmlspecialchars($position); ?></div>
              <div class="value empty" id="sel_<?php echo $posKey; ?>">Not selected</div>
            </div><!-- /.selection-item -->
          <?php endforeach; ?>

          <button class="btn-submit" id="btn_submit" disabled onclick="showConfirmModal()">
            Select Candidates
          </button>
        </div><!-- /.selection-panel -->

      </div><!-- /.content-wrapper -->
    </div><!-- /.container-main -->

    <?php include '../../components/footer.php'; ?>

    <?php renderLoginSuccessOverlay(); ?>
    <?php renderLoginInvalidOverlay(); ?>

    <!-- Confirm Vote Modal -->
    <div id="confirmOverlay" class="status-overlay confirm-modal" style="display: none;">
      <div class="status-card">
        <div class="status-icon">
          <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="status-title">Confirm Your Vote</div>
        <div class="status-subtitle">Please review your selections before submitting:</div>

        <div id="confirmVoteDetails" class="vote-details"></div>

        <p class="warning-text">
          <i class="fas fa-exclamation-triangle"></i> Once submitted, you cannot change your vote.
        </p>

        <button class="status-btn" id="confirmSubmitBtn">Submit Vote</button>
        <button class="btn-cancel" onclick="hideConfirmModal()">Cancel</button>
      </div><!-- /.status-card -->
    </div><!-- /#confirmOverlay -->

    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
      function showConfirmModal() {
        const detailsDiv = document.getElementById('confirmVoteDetails');
        let html = '';

        positions.forEach(pos => {
          const posKey = pos.toLowerCase().replace(/\s+/g, '');
          const candidateName = selectedCandidates[posKey] || 'Not selected';
          html += '<div class="vote-item">';
          html += '<span class="vote-position">' + pos + '</span>';
          html += '<span class="vote-candidate">' + candidateName + '</span>';
          html += '</div>';
        });

        detailsDiv.innerHTML = html;
        document.getElementById('confirmOverlay').style.display = 'flex';
        document.getElementById('confirmOverlay').classList.add('show');

        document.getElementById('confirmSubmitBtn').onclick = function() {
          hideConfirmModal();
          submitVote();
        };
      }

      function hideConfirmModal() {
        document.getElementById('confirmOverlay').style.display = 'none';
        document.getElementById('confirmOverlay').classList.remove('show');
      }

      const originalShowSuccessOverlay = showSuccessOverlay;
      showSuccessOverlay = function(title, subtitle, buttonText, redirectUrl, otp) {
        document.getElementById('successTitle').textContent = title || 'Success!';
        document.getElementById('successSubtitle').innerHTML = subtitle || 'Your action was completed successfully.';

        const btn = document.getElementById('successBtn');
        if (buttonText) {
          btn.textContent = buttonText;
        }

        document.getElementById('otpContainer').style.display = 'none';
        document.querySelector('.auto-redirect').style.display = 'none';

        btn.onclick = function() {
          hideSuccessOverlay();
        };

        const container = document.getElementById('confettiContainer');
        container.innerHTML = '';
        const colors = ['#222', '#444', '#666', '#888', '#aaa'];

        for (let i = 0; i < 80; i++) {
          const confetti = document.createElement('div');
          confetti.className = 'confetti';
          confetti.style.left = Math.random() * 100 + '%';
          confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
          confetti.style.animationDelay = Math.random() * 2 + 's';
          confetti.style.width = (Math.random() * 8 + 6) + 'px';
          confetti.style.height = (Math.random() * 8 + 6) + 'px';
          confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
          container.appendChild(confetti);
        }

        document.getElementById('successOverlay').classList.add('show');
      };
    </script>

    <script>
      (function() {
        const container = document.getElementById('floatingLogos');
        const logoUrl = '../../img/logo/PTCI-logo.png';
        const count = 40;

        for (let i = 0; i < count; i++) {
          const logo = document.createElement('div');
          logo.className = 'floating-logo';
          logo.style.top = Math.random() * 100 + '%';
          logo.style.left = Math.random() * 100 + '%';
          logo.style.animationDelay = (Math.random() * 5) + 's';
          logo.style.animationDuration = (15 + Math.random() * 10) + 's';

          const img = document.createElement('img');
          img.src = logoUrl;
          img.alt = 'PTCI';
          img.className = 'theme-img';
          img.dataset.light = logoUrl;
          img.dataset.dark = '../../assets/img/ic2-namberwan.png';

          logo.appendChild(img);
          container.appendChild(logo);
        }
      })();

      function updateFloatingLogos() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const floatingLogos = document.querySelectorAll('.floating-logo img');
        floatingLogos.forEach(img => {
          img.src = isDark ? img.dataset.dark : img.dataset.light;
        });
      }

      const originalToggleTheme = window.toggleTheme;
      window.toggleTheme = function() {
        if (originalToggleTheme) originalToggleTheme();
        setTimeout(updateFloatingLogos, 100);
      };

      setTimeout(updateFloatingLogos, 100);

      const selectedCandidates = {};
      const selectedCandidateIds = {};

      const positions = <?php echo json_encode(array_values($displayPositions)); ?>;

      function selectCandidate(id, name, type, candidateId) {
        document.querySelectorAll('[id^="' + type + '_"]').forEach(card => card.classList.remove('selected'));

        selectedCandidates[type] = name;
        selectedCandidateIds[type] = candidateId;

        document.getElementById(id).classList.add('selected');

        const el = document.getElementById('sel_' + type);
        if (el) {
          el.textContent = name;
          el.classList.remove('empty');
        }

        updateSubmitButton();
      }

      function updateSubmitButton() {
        const btn = document.getElementById('btn_submit');
        const allSelected = positions.every(pos => {
          const posKey = pos.toLowerCase().replace(/\s+/g, '');
          return selectedCandidates[posKey];
        });

        if (allSelected && positions.length > 0) {
          btn.disabled = false;
          btn.textContent = 'Submit Vote';
        } else {
          btn.disabled = true;
          btn.textContent = 'Select Candidates';
        }
      }

      function submitVote() {
        const btn = document.getElementById('btn_submit');

        const votes = [];
        positions.forEach(pos => {
          const posKey = pos.toLowerCase().replace(/\s+/g, '');
          if (selectedCandidates[posKey] && selectedCandidateIds[posKey]) {
            votes.push({
              position: pos,
              cand_id: selectedCandidateIds[posKey],
              candidate_name: selectedCandidates[posKey]
            });
          }
        });

        if (votes.length === 0) return;

        btn.disabled = true;
        btn.textContent = 'Submitting...';

        fetch('../../backend/api/submitVote.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ votes: votes })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const voteDetails = votes.map(v => '<strong>' + v.position + ':</strong> ' + v.candidate_name).join('<br>');
            showSuccessOverlay(
              'Vote Submitted!',
              'Your vote has been recorded successfully.<br>' + voteDetails,
              'OK',
              null
            );

            document.querySelectorAll('.candidate-card').forEach(card => {
              card.style.pointerEvents = 'none';
              card.style.opacity = '0.5';
            });
            btn.disabled = true;
            btn.textContent = 'Voted';

            setTimeout(function() {
              fetch('../../backend/api/logout.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                }
              })
              .then(response => response.json())
              .then(logoutData => {
                window.location.href = '../../public/pages/home.php';
              })
              .catch(err => {
                window.location.href = '../../public/pages/home.php';
              });
            }, 3000);
          } else {
            showInvalidOverlay('Vote Failed', data.message || 'An error occurred while casting your vote.');
            btn.disabled = false;
            btn.textContent = 'Submit Vote';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showInvalidOverlay('Error', 'An error occurred. Please try again.');
          btn.disabled = false;
          btn.textContent = 'Submit Vote';
        });
      }
    </script>
  </body>
</html>