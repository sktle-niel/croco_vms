<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PTCI Student Council</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/css/global.css" />
    <link rel="stylesheet" href="../../assets/css/form.css" />
    <link rel="stylesheet" href="../../assets/css/status.css" />
    <link rel="stylesheet" href="../../assets/css/navbar.css" />
    <link rel="stylesheet" href="../../assets/css/theme.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../../img/logo/PTCI-logo.png">
</head>

<body class="form-body">

    <!-- Navigation -->
    <?php include '../components/navigationbar.php'; ?>

    <!-- Floating Logos Background -->
    <div class="form-floating-logos" id="formFloatingLogos"></div>

    <div class="form-container">
        <div class="form-card">

            <!-- Logo Section -->
            <div class="form-logo">
                <img src="../../img/logo/PTCI-logo.png" alt="PTCI Logo">
            </div>

            <!-- Title -->
            <div class="form-header">
                <h1>Create Account</h1>
                <p>Register to participate in the PTCI Student Council Election</p>
            </div>

            <!-- Error Alert -->
            <div class="form-alert" id="errorAlert">
                <i class="fas fa-exclamation-circle"></i>
                <span id="errorMessage">An error occurred. Please try again.</span>
            </div>

            <!-- Registration Form -->
            <form id="register">
                <div class="form-group">
                    <label for="schoolID">Student ID / LRN</label>
                    <input type="text" name="SchoolID" id="schoolID" class="form-control" placeholder="Enter Student ID or LRN" required />
                </div>

                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" name="AccountName" id="fullName" class="form-control" placeholder="Enter Full Name" required />
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <select class="form-control" id="department" name="Department" required>
                        <option value="">Select Department</option>
                        <option value="Senior Highschool">Senior Highschool</option>
                        <option value="Associate in Computer Technology">Associate in Computer Technology</option>
                        <option value="Bachelor of Science in Hospitality Management">Bachelor of Science in Hospitality Management</option>
                        <option value="Bachelor of Science in Information Communication Technology">Bachelor of Science in Information Communication Technology</option>
                        <option value="Bachelor of Science in Information Systems">Bachelor of Science in Information Systems</option>
                        <option value="Bachelor of Science in Office Administration">Bachelor of Science in Office Administration</option>
                    </select>
                </div>

                <button type="submit" class="form-btn-submit" id="submitBtn">
                    <i class="fas fa-user-plus"></i>
                    Register
                </button>
            </form>

            <!-- Login Link -->
            <div class="form-link-login">
                <p>Already have an account? <a href="../../auth/form.php">Log In</a></p>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Success Overlay -->
    <?php
    include '../../public/components/successMessage.php';
    renderSuccessOverlay();
    ?>

    <!-- Invalid Overlay -->
    <?php
    include '../../public/components/invalidMessage.php';
    renderInvalidOverlay();
    ?>

    <script>
        /* ── Floating Logos ── */
        (function () {
            const container = document.getElementById('formFloatingLogos');
            const logoUrl   = '../../img/logo/PTCI-logo.png';
            const count     = 20;

            for (let i = 0; i < count; i++) {
                const logo = document.createElement('div');
                logo.className = 'form-floating-logo';
                logo.style.top              = Math.random() * 100 + '%';
                logo.style.left             = Math.random() * 100 + '%';
                logo.style.animationDelay   = (Math.random() * 5) + 's';
                logo.style.animationDuration = (20 + Math.random() * 10) + 's';

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
        
        /* ── Update Floating Logos for Dark Mode ── */
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

        /* ── Form Submission ── */
        document.getElementById('register').addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn   = document.getElementById('submitBtn');
            const errorAlert  = document.getElementById('errorAlert');
            const errorMessage = document.getElementById('errorMessage');

            const formData = new FormData(this);

            // Disable button while submitting
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
            errorAlert.classList.remove('show');

            fetch('../../backend/create/createAccount.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('HTTP error ' + response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response:', JSON.stringify(data));

                if (data.success) {
                    // Show success overlay with OTP
                    showSuccessOverlay(
                        'Registration Successful!',
                        'Your account has been created. Please copy your OTP below.',
                        'Proceed to Login',
                        '../../auth/form.php',
                        data.otp
                    );
                } else {
                    if (data.error === 'exists') {
                        showInvalidOverlay(
                            'Already Registered',
                            'This Student ID / LRN is already registered.<br>Please use a different ID or <a href="../../auth/form.php">log in</a>.'
                        );
                    } else if (data.error === 'empty') {
                        errorAlert.classList.add('show');
                        errorMessage.textContent = 'Please fill in all fields.';
                    } else {
                        errorAlert.classList.add('show');
                        errorMessage.textContent = 'Error: ' + (data.error || 'Unknown error.');
                    }
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                errorAlert.classList.add('show');
                errorMessage.textContent = 'Network error. Please try again.';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Register';
            });
        });
    </script>

</body>
</html>

