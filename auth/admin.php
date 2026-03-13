<?php
require_once __DIR__ . '/../backend/include.php';
require_once __DIR__ . '/../components/invalidMessage.php';
require_once __DIR__ . '/../components/successMessage.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ../administrator/main.php?page=dashboard');
    exit;
}

$view = $_GET['view'] ?? 'login';

// Guard OTP view
if ($view === 'otp' && empty($_SESSION['admin_pending_email'])) {
    header('Location: ../auth/admin.php');
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
    <style>
        .otp-input {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 10px;
            text-align: center;
            font-family: 'Courier New', monospace;
        }
        .otp-hint {
            font-size: 12px;
            color: #888;
            text-align: center;
            margin-top: -8px;
            margin-bottom: 12px;
        }
        [data-theme="dark"] .otp-hint { color: #666; }
    </style>
</head>
<body class="form-body">
    <?php include '../components/navigationbar.php'; ?>
    <div class="form-floating-logos" id="formFloatingLogos"></div>
    <?php renderLoginInvalidOverlay(); ?>
    <?php renderLoginSuccessOverlay(); ?>

    <div class="form-container">
        <div class="form-card">

            <div class="form-logo">
                <img src="../img/logo/PTCI-logo.png" alt="PTCI Logo">
            </div>

            <?php if ($view === 'login'): ?>
            <!-- ── LOGIN FORM ── -->
            <div class="form-header">
                <h1>Admin Portal</h1>
                <p>Log in to manage the election</p>
            </div>
            <form id="loginForm" action="../backend/validation/adminValidation.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" class="form-control" id="email"
                           placeholder="Enter Admin Email" required />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password"
                           placeholder="Enter Password" required />
                </div>
                <button type="submit" class="form-btn-submit" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
            </form>
            <div class="form-link-back">
                <a href="../public/pages/home.php">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>

            <?php elseif ($view === 'otp'): ?>
            <!-- ── OTP FORM ── -->
            <div class="form-header">
                <h1>Check Your Email</h1>
                <p>We sent a 6-digit code to<br>
                   <strong><?php echo htmlspecialchars($_SESSION['admin_pending_email']); ?></strong>
                </p>
            </div>
            <form id="otpForm" action="../backend/validation/adminOTPValidation.php" method="POST">
                <div class="form-group">
                    <label for="otp">One-Time Password</label>
                    <input type="text" name="otp" class="form-control otp-input" id="otp"
                           placeholder="000000" maxlength="6" inputmode="numeric"
                           pattern="\d{6}" required autofocus />
                </div>
                <p class="otp-hint">Code expires in 10 minutes</p>
                <button type="submit" class="form-btn-submit" id="otpBtn">
                    <i class="fas fa-shield-alt"></i> Verify OTP
                </button>
            </form>
            <div class="form-link-back">
                <a href="../auth/admin.php">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error     = urlParams.get('error');
            const success   = urlParams.get('success');
            const view      = '<?php echo $view; ?>';

            if (error) {
                setTimeout(() => showInvalidOverlay('Error', decodeURIComponent(error.replace(/\+/g, ' '))), 400);
                setTimeout(() => {
                    const base = window.location.pathname + (view !== 'login' ? '?view=' + view : '');
                    window.history.replaceState({}, document.title, base);
                }, 3000);
            }

            if (success === 'true') {
                setTimeout(() => showLoginSuccessOverlay(
                    'Login Successful!',
                    'Welcome back, Admin! Redirecting to the dashboard...',
                    'Go to Dashboard',
                    '../administrator/'
                ), 400);
            }
        })();

        // Digits only for OTP input
        const otpInput = document.getElementById('otp');
        if (otpInput) {
            otpInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });
        }

        <?php if ($view === 'login'): ?>
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        loginForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending OTP...';
            // Let the form submit normally so PHP redirect works
        });
        <?php endif; ?>

        <?php if ($view === 'otp'): ?>
        const otpForm = document.getElementById('otpForm');
        const otpBtn  = document.getElementById('otpBtn');
        otpForm.addEventListener('submit', function() {
            otpBtn.disabled = true;
            otpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            // Let the form submit normally so PHP redirect works
        });
        <?php endif; ?>

        // Floating logos
        (function() {
            const container = document.getElementById('formFloatingLogos');
            const logoUrl   = '../img/logo/PTCI-logo.png';
            for (let i = 0; i < 25; i++) {
                const logo = document.createElement('div');
                logo.className = 'form-floating-logo';
                logo.style.cssText = `top:${Math.random()*100}%;left:${Math.random()*100}%;animation-delay:${Math.random()*5}s;animation-duration:${20+Math.random()*10}s`;
                const img = document.createElement('img');
                img.src = logoUrl; img.alt = 'PTCI'; img.className = 'theme-img';
                img.dataset.light = logoUrl;
                img.dataset.dark  = '../assets/img/ic2-namberwan.png';
                logo.appendChild(img);
                container.appendChild(logo);
            }
        })();

        function updateFloatingLogos() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            document.querySelectorAll('.form-floating-logo img').forEach(img => {
                img.src = isDark ? img.dataset.dark : img.dataset.light;
            });
        }
        setTimeout(updateFloatingLogos, 100);
        const _orig = window.toggleTheme;
        window.toggleTheme = function() { if (_orig) _orig(); setTimeout(updateFloatingLogos, 100); };
    </script>
</body>
</html>