<?php
// Main entry point for administrator pages
$page = $_GET['page'] ?? 'dashboard';

// Define allowed pages
$allowed_pages = ['dashboard', 'votersAccount', 'result', 'manage', 'setup'];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Include session check
require_once __DIR__ . '/../backend/include.php';

// Check if admin is logged in (commented out for UI testing)
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header('Location: ../auth/admin.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - PTCI Student Council</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/theme.css" />
    <link rel="stylesheet" href="css/main.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../img/logo/PTCI-logo.png">
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Header -->
            <header class="admin-header">
                <div class="header-title">
                    <h1><?php echo ucfirst($page); ?></h1>
                </div>
                <div class="header-actions">
                    <div class="theme-toggle" id="themeToggle" onclick="toggleTheme()">
                        <i class="fas fa-sun" id="themeIcon"></i>
                    </div>
                    <a href="../auth/logout.php" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="admin-content">
                <?php include 'pages/' . $page . '.php'; ?>
            </div>
        </main>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>
