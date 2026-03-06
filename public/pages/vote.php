<?php
// Check if user is logged in
require_once '../../auth/session.php';
?>

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
    <link rel="stylesheet" href="../../assets/css/navbar.css" />
    <link rel="stylesheet" href="../../assets/css/theme.css" />
    <link rel="stylesheet" href="../../assets/css/vote.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../../img/logo/PTCI-logo.png" />
  </head>

  <body>
    <!-- Floating Logos Background -->
    <div class="floating-logos-bg" id="floatingLogos"></div>

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

            <div class="candidate-card" id="pres_2" onclick="selectCandidate('pres_2', 'Velly Ibanez', 'president')">
              <img class="avatar" src="../../img/candidatesImg/velly.jpg" onerror="this.src='../../img/logo/PTCI-logo.png'" alt="Candidate">
              <div class="info">
                <div class="name">Velly Ibanez</div>
                <div class="dept">BSOA - 3rd Year</div>
                <div class="desc">A dedicated leader with strong communication skills and passion for student empowerment.</div>
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

            <div class="candidate-card" id="vpres_2" onclick="selectCandidate('vpres_2', 'Lovelie Reyes', 'vice')">
              <img class="avatar" src="../../img/candidatesImg/lovelie.jpeg" onerror="this.src='../../img/logo/PTCI-logo.png'" alt="Candidate">
              <div class="info">
                <div class="name">Lovelie Reyes</div>
                <div class="dept">BSHM - 3rd Year</div>
                <div class="desc">An enthusiastic advocate for student welfare and progress.</div>
              </div>
              <div class="check"><i class="fas fa-check"></i></div>
            </div>

            <div class="candidate-card" id="vpres_3" onclick="selectCandidate('vpres_3', 'Jamie Lopez', 'vice')">
              <img class="avatar" src="../../img/candidatesImg/jamie.jpeg" onerror="this.src='../../img/logo/PTCI-logo.png'" alt="Candidate">
              <div class="info">
                <div class="name">Jamie Lopez</div>
                <div class="dept">BSOA - 4th Year</div>
                <div class="desc">A proactive leader focused on unity and student development.</div>
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
      
      // Update floating logos when theme changes
      function updateFloatingLogos() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const floatingLogos = document.querySelectorAll('.floating-logo img');
        floatingLogos.forEach(img => {
          img.src = isDark ? img.dataset.dark : img.dataset.light;
        });
      }
      
      // Listen for theme changes
      const originalToggleTheme = window.toggleTheme;
      window.toggleTheme = function() {
        if (originalToggleTheme) originalToggleTheme();
        setTimeout(updateFloatingLogos, 100);
      };
      
      // Apply correct logo on page load
      setTimeout(updateFloatingLogos, 100);

      let selectedPresident = null;
      let selectedVicePresident = null;

      function selectCandidate(id, name, type) {
        if (type === 'president') {
          document.querySelectorAll('[id^="pres_"]').forEach(card => card.classList.remove('selected'));
          selectedPresident = name;
        } else {
          document.querySelectorAll('[id^="vpres_"]').forEach(card => card.classList.remove('selected'));
          selectedVicePresident = name;
        }

        document.getElementById(id).classList.add('selected');

        if (type === 'president') {
          const el = document.getElementById('sel_pres');
          el.textContent = name;
          el.classList.remove('empty');
        } else {
          const el = document.getElementById('sel_vpres');
          el.textContent = name;
          el.classList.remove('empty');
        }

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


