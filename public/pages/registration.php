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
    <link rel="stylesheet" href="../../assets/css/registration.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../../img/logo/PTCI-logo.png">
</head>
<body class="registration-body">
    <!-- Floating Logos Background -->
    <div class="floating-logos" id="floatingLogos"></div>

    <div class="registration-container">
        <div class="registration-card">
            <!-- Logo Section -->
            <div class="registration-logo">
                <img src="../../img/logo/PTCI-logo.png" alt="PTCI Logo">
            </div>

            <!-- Title -->
            <div class="registration-header">
                <h1>Create Account</h1>
                <p>Register to participate in the PTCI Student Council Election</p>
            </div>

            <!-- Alerts -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
                <div class="alert alert-success-custom">
                    <i class="fas fa-check-circle"></i>
                    Registration successful! You can now <a href="login.php">log in</a>.
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
                <div class="alert alert-danger-custom">
                    <i class="fas fa-exclamation-circle"></i>
                    This Student ID/LRN is already registered. Please use a different ID or <a href="login.php">log in</a> if this is your account.
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form action="" method="POST" id="register">
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

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i>
                    Register
                </button>
            </form>

            <!-- Login Link -->
            <div class="login-link">
                <p>Already have an account? <a href="../../auth/form.php">Log In</a></p>
            </div>
    </div>

    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Generate floating logos
        (function() {
            const container = document.getElementById('floatingLogos');
            const logoUrl = '../../img/logo/PTCI-logo.png';
            const count = 25;
            
            for (let i = 0; i < count; i++) {
                const logo = document.createElement('div');
                logo.className = 'floating-logo';
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
