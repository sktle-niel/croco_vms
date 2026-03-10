<?php
require_once __DIR__ . '/../backend/include.php';
require_once __DIR__ . '/../components/invalidMessage.php';
require_once __DIR__ . '/../components/successMessage.php';

// Check if admin is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ../administrator/main.php?page=dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PTCI Student Council</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/form.css" />
    <link rel="stylesheet" href="../assets/css/status.css" />
    <link rel="stylesheet" href="../assets/css/navbar.css" />
    <link rel="stylesheet" href="../assets/css/theme.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../img/logo/PTCI-logo.png">
</head>
<body class="form-body">
    <!-- Navigation -->
    <?php include '../components/navigationbar.php'; ?>

    <!-- Floating Logos Background -->
    <div class="form-floating-logos" id="formFloatingLogos"></div>

    <!-- Login Invalid Message Overlay -->
    <?php renderLoginInvalidOverlay(); ?>

    <!-- Login Success Message Overlay -->
    <?php renderLoginSuccessOverlay(); ?>

    <div class="form-container">
        <div class="form-card">
            <!-- Logo Section -->
            <div class="form-logo">
                <img src="../img/logo/PTCI-logo.png" alt="PTCI Logo">
            </div>

            <!-- Title -->
            <div class="form-header">
                <h1>Admin Portal</h1>
                <p>Log in to manage the election</p>
            </div>

            <!-- Admin Login Form -->
            <form id="loginForm" action="../backend/validation/adminValidation.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter Admin Email" required />
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password" required />
                </div>

                <button type="submit" class="form-btn-submit" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    Log In
                </button>
            </form>

            <!-- Go Back -->
            <div class="form-link-back">
                <a href="../public/pages/home.php">
                    <i class="fas fa-arrow-left"></i>
                    Go Back
                </a>
            </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Check for URL parameters (error or success)
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const success = urlParams.get('success');
            
            if (error) {
                let title = 'Login Failed';
                let message = decodeURIComponent(error.replace(/\+/g, ' '));
                
                setTimeout(function() {
                    showInvalidOverlay(title, message);
                }, 500);
                
                // Remove error parameter from URL
                setTimeout(function() {
                    const newUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                }, 3000);
            }
            
            if (success === 'true') {
                setTimeout(function() {
                    showLoginSuccessOverlay(
                        'Login Successful!',
                        'Welcome back, Admin! Redirecting you to the dashboard...',
                        'Go to Dashboard',
                        '../administrator/main.php?page=dashboard'
                    );
                }, 500);
                
                // Remove success parameter from URL
                setTimeout(function() {
                    const newUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                }, 6000);
            }
        })();
        
        // Login form submission with fetch
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (email && password) {
                // Disable button while submitting
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
                
                const formData = new FormData(loginForm);
                
                fetch('../backend/validation/adminValidation.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Get the redirect URL from response headers or redirect
                    window.location.href = response.url;
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Log In';
                    showInvalidOverlay('Login Failed', 'An error occurred. Please try again.');
                });
            }
        });
        
        // Generate floating logos
        (function() {
            const container = document.getElementById('formFloatingLogos');
            const logoUrl = '../img/logo/PTCI-logo.png';
            const count = 25;
            
            for (let i = 0; i < count; i++) {
                const logo = document.createElement('div');
                logo.className = 'form-floating-logo';
                logo.style.top = Math.random() * 100 + '%';
                logo.style.left = Math.random() * 100 + '%';
                logo.style.animationDelay = (Math.random() * 5) + 's';
                logo.style.animationDuration = (20 + Math.random() * 10) + 's';
                
                const img = document.createElement('img');
                img.src = logoUrl;
                img.alt = 'PTCI';
                img.className = 'theme-img';
                img.dataset.light = logoUrl;
                img.dataset.dark = '../assets/img/ic2-namberwan.png';
                
                logo.appendChild(img);
                container.appendChild(logo);
            }
        })();
        
        // Update floating logos for dark mode
        function updateFloatingLogos() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const floatingLogos = document.querySelectorAll('.form-floating-logo img');
            floatingLogos.forEach(img => {
                img.src = isDark ? img.dataset.dark : img.dataset.light;
            });
        }
        
        // Apply correct logo on page load
        setTimeout(updateFloatingLogos, 100);
        
        // Listen for theme changes
        const originalToggleTheme = window.toggleTheme;
        window.toggleTheme = function() {
            if (originalToggleTheme) originalToggleTheme();
            setTimeout(updateFloatingLogos, 100);
        };
    </script>
</body>
</html>
