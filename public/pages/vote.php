<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PTCI Student Council Election</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/css/global.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../../img/logo/PTCI-logo.png" />
  </head>

  <body>
    <!-- Navigation -->
    <?php include '../components/navigationbar.php'; ?>

    <!-- Main Content -->
    <div class="container-main mt-5">
      <!-- Header -->
      <div class="header">
        <h1>Cast Your Vote</h1>
        <p>Select your candidates for each position</p>
        
        <!-- Instructions Box -->
        <div class="instructions-box">
          <div class="instructions-title"><i class="fas fa-info-circle"></i> Instructions</div>
          <ul class="instructions-list">
            <li>Select your preferred candidate for each position by clicking on their card</li>
            <li>Review your selections in the panel on the right before submitting</li>
            <li>Once submitted, changes cannot be made. Ensure your choices are final</li>
          </ul>
        </div>
      </div>

      <!-- Two Column Layout -->
      <div class="content-wrapper">
        <!-- Left Side - Candidates -->
        <div class="candidates-side">
          <!-- President Section -->
          <div class="section">
            <div class="position-title">President</div>
            
            <div class="candidate-card" id="pres_1" onclick="selectCandidate('pres_1', 'Generose Nastor', 'president')">
              <img class="avatar" src="../../img/candidatesImg/gen.jpg" onerror="this.src='../../img/logo/PTCI-logo.png'" alt="Candidate">
              <div class="info">
                <div class="name">Generose Nastor</div>
                <div class="dept">BSIT - 3rd Year</div>
                <div class="desc">A visionary leader committed to fostering innovation and excellence.</div>
              </div>
              <div class="check"><i class="fas fa-check"></i></div>
            </div>
          </div>

          <!-- Vice President Section -->
          <div class="section">
            <div class="position-title">Vice President</div>
            
            <div class="candidate-card" id="vpres_1" onclick="selectCandidate('vpres_1', 'Lennith Castro', 'vice')">
              <img class="avatar" src="../../img/candidatesImg/lennith.jpg" onerror="this.src='../../img/logo/PTCI-logo.png'" alt="Candidate">
              <div class="info">
                <div class="name">Lennith Castro</div>
                <div class="dept">BSIT - 3rd Year</div>
                <div class="desc">An enthusiastic advocate for student welfare and progress.</div>
              </div>
              <div class="check"><i class="fas fa-check"></i></div>
            </div>
          </div>
        </div>

        <!-- Right Side - Selection Panel -->
        <div class="selection-panel">
          <h3>Your Selection</h3>
          
          <div class="selection-item">
            <div class="label">President</div>
            <div class="value empty" id="sel_pres">Not selected</div>
          </div>
          
          <div class="selection-item">
            <div class="label">Vice President</div>
            <div class="value empty" id="sel_vpres">Not selected</div>
          </div>

          <button class="btn-submit" id="btn_submit" disabled onclick="submitVote()">Submit Vote</button>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <?php include '../components/footer.php'; ?>

    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
      let selectedPresident = null;
      let selectedVicePresident = null;

      function selectCandidate(id, name, type) {
        if (type === 'president') {
          // Deselect all president cards
          document.querySelectorAll('[id^="pres_"]').forEach(card => card.classList.remove('selected'));
          selectedPresident = name;
        } else {
          // Deselect all vice president cards
          document.querySelectorAll('[id^="vpres_"]').forEach(card => card.classList.remove('selected'));
          selectedVicePresident = name;
        }

        // Select clicked card
        document.getElementById(id).classList.add('selected');

        // Update summary
        if (type === 'president') {
          const el = document.getElementById('sel_pres');
          el.textContent = name;
          el.classList.remove('empty');
        } else {
          const el = document.getElementById('sel_vpres');
          el.textContent = name;
          el.classList.remove('empty');
        }

        // Update button state
        updateSubmitButton();
      }

      function updateSubmitButton() {
        const btn = document.getElementById('btn_submit');
        if (selectedPresident && selectedVicePresident) {
          btn.disabled = false;
          btn.textContent = 'Submit Vote';
        } else {
          btn.disabled = true;
          btn.textContent = 'Select Candidates';
        }
      }

      function submitVote() {
        if (!selectedPresident || !selectedVicePresident) return;
        
        alert('Vote submitted successfully!\n\nPresident: ' + selectedPresident + '\nVice President: ' + selectedVicePresident);
        
        // Reset (demo only)
        selectedPresident = null;
        selectedVicePresident = null;
        document.querySelectorAll('.candidate-card').forEach(c => c.classList.remove('selected'));
        document.getElementById('sel_pres').textContent = 'Not selected';
        document.getElementById('sel_pres').classList.add('empty');
        document.getElementById('sel_vpres').textContent = 'Not selected';
        document.getElementById('sel_vpres').classList.add('empty');
        updateSubmitButton();
      }
    </script>
  </body>
</html>

