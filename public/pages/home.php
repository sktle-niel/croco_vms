<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTCI Student Council Election</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/css/global.css" />
    <link rel="stylesheet" href="../../assets/css/home.css" />
    <link rel="stylesheet" href="../../assets/css/navbar.css" />
    <link rel="stylesheet" href="../../assets/css/theme.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../../img/logo/PTCI-logo.png">
</head>
<body>
    <!-- Navigation -->
    <?php include '../../components/navigationbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <!-- Floating Logos Background -->
        <div class="floating-logos" id="floatingLogos"></div>
        
        <div class="hero-content">
            <div class="hero-logos">
                <img src="../../img/logo/PTCI-logo.png" alt="PTCI Logo">
            </div>
            <h1>Vote for Your Future</h1>
            <p class="subtitle">Your voice matters. Participate in the PTCI Student Council Election and help shape the future of our campus community.</p>
            <div class="hero-buttons">
                <a href="vote.php" class="btn-primary-custom">Cast Your Vote</a>
                <a href="#candidates" class="btn-secondary-custom">View Candidates</a>
            </div>
    </section>

    <!-- Candidates Preview -->
    <section class="candidates-section" id="candidates">
        <div class="section-title">
            <h2>Meet the Candidates</h2>
            <p>Learn about the candidates running for office</p>
        </div>
        <div class="candidates-grid">
            <div class="candidate-preview">
                <img src="../../img/candidatesImg/gen.jpg" alt="Generose Nastor" onerror="this.src='../../img/logo/PTCI-logo.png'">
                <h4>Generose Nastor</h4>
                <p>President Candidate</p>
            </div>
            <div class="candidate-preview">
                <img src="../../img/candidatesImg/lennith.jpg" alt="Lennith Castro" onerror="this.src='../../img/logo/PTCI-logo.png'">
                <h4>Lennith Castro</h4>
                <p>Vice President Candidate</p>
            </div>
            <div class="candidate-preview">
                <img src="../../img/candidatesImg/velly.jpg" alt="Velly Ibanez" onerror="this.src='../../img/logo/PTCI-logo.png'">
                <h4>Velly Ibanez</h4>
                <p>President Candidate</p>
            </div>
            <div class="candidate-preview">
                <img src="../../img/candidatesImg/lovelie.jpeg" alt="Lovelie Reyes" onerror="this.src='../../img/logo/PTCI-logo.png'">
                <h4>Lovelie Reyes</h4>
                <p>Vice President Candidate</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="section-title">
            <h2>How It Works</h2>
            <p>Simple steps to cast your vote</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h4>Register</h4>
                <p>Create your student account to participate in the election</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4>Review Candidates</h4>
                <p>Learn about the candidates and their platforms</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-vote-yea"></i>
                </div>
                <h4>Cast Your Vote</h4>
                <p>Select your preferred candidates and submit your vote</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../../components/footer.php'; ?>

    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Generate floating logos - always use PTCI-logo.png
        // The theme toggle will handle changing to ic2-namberwan.png in dark mode
        (function() {
            const container = document.getElementById('floatingLogos');
            const logoUrl = '../../img/logo/PTCI-logo.png';
            const count = 50;
            
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
    </script>
</body>
</html>


