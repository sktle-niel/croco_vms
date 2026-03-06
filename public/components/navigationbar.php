<!-- Navigation -->
<nav class="navbar navbar-expand-md fixed-top">
  <div class="container">
    <a class="navbar-brand" href="../pages/home.php">
      <img class="brand-logo" src="../../assets/img/LOGO.png" alt="IC2 Logo" />
      <span class="brand-text">
        <span class="brand-main">PALAWAN TECHNOLOGICAL COLLEGE</span>
        <span class="brand-divider">|</span>
        <span class="brand-sub">INFORMATION COMMUNICATION CLUB</span>
      </span>
    </a>
    
    <div class="d-flex align-items-center gap-3">
      <!-- Theme Toggle -->
      <div class="theme-toggle" id="themeToggle" onclick="toggleTheme()">
        <i class="fas fa-sun" id="themeIcon"></i>
      </div>
    </div>
  </div>
</nav>

<script>
  // Check for saved theme preference
  const savedTheme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);

  function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
  }

  function updateThemeIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (theme === 'dark') {
      icon.className = 'fas fa-moon';
    } else {
      icon.className = 'fas fa-sun';
    }
  }
</script>

