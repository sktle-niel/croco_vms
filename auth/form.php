<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PTCI Student Council</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/form.css" />
    <link rel="stylesheet" href="../assets/css/status.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../img/logo/PTCI-logo.png">
</head>
<body class="form-body">
    <!-- Floating Logos Background -->
    <div class="form-floating-logos" id="formFloatingLogos"></div>

    <div class="form-container">
        <div class="form-card">
            <!-- Logo Section -->
            <div class="form-logo">
                <img src="../img/logo/PTCI-logo.png" alt="PTCI Logo">
            </div>

            <!-- Title -->
            <div class="form-header">
                <h1>Welcome Back</h1>
                <p>Log in to cast your vote</p>
            </div>

            <!-- Alert -->
            <?php if (isset($_GET['error'])): ?>
                <div class="form-alert show">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="../backend/validation/login_process.php" method="POST">
                <div class="form-group">
                    <label for="SchoolID">Student ID / LRN</label>
                    <input type="text" name="SchoolID" class="form-control" id="SchoolID" placeholder="Enter Student ID or LRN" required />
                </div>

                <div class="form-group">
                    <label for="otp">One Time Password</label>
                    <input type="password" name="otp" class="form-control" id="otp" placeholder="Enter OTP" required />
                </div>

                <button type="submit" class="form-btn-submit">
                    <i class="fas fa-sign-in-alt"></i>
                    Log In
                </button>
            </form>

            <!-- Register Link -->
            <div class="form-link-register">
                <p>Don't have an account? <a href="../public/pages/registration.php">Register</a></p>
            </div>

            <!-- Go Back -->
            <div class="form-link-back">
                <a href="../public/pages/home.php">
                    <i class="fas fa-arrow-left"></i>
                    Go Back
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
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
                
                logo.appendChild(img);
                container.appendChild(logo);
            }
        })();
    </script>
</body>
</html>

