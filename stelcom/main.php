<?php
// Main entry point for STELCOM pages
$page = $_GET['page'] ?? 'dashboard';

// Map clean routes to page files
$route_map = [
    'dashboard' => 'dashboard',
    'voters-account' => 'votersAccount',
    'final-votes' => 'finalVotes', 
    'results' => 'result',
    'manage' => 'manage',
    'account' => 'account'
];

// Get page name from route or query param (backward compatible)
$route = $_GET['route'] ?? $_GET['page'] ?? 'dashboard';

if (isset($route_map[$route])) {
    $page = $route_map[$route];
} elseif (in_array($route, array_values($route_map))) {
    $page = $route;
} else {
    $page = 'dashboard';
}

// Include backend
require_once __DIR__ . '/../backend/include.php';

// Auth check (uncommented and fixed)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../auth/admin.php');
    exit;
}

// Moderator redirect
if (isset($_SESSION['admin_user_type']) && $_SESSION['admin_user_type'] === 'moderator') {
    header('Location: ../moderator/main.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STELCOM Portal - <?php echo ucfirst($page); ?></title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/account.css">
    <link rel="stylesheet" href="css/manage.css">
    <link rel="stylesheet" href="css/results.css">
    <link rel="stylesheet" href="css/voting.css">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/logo/PTCI-logo.png">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include 'components/sidebar.php'; ?>
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-title">
                    <h1><?php echo ucfirst(str_replace('-', ' ', $route)); ?></h1>
                </div>
                <div class="header-actions">
                    <div class="theme-toggle" onclick="toggleTheme()">
                        <i class="fas fa-sun" id="themeIcon"></i>
                    </div>
                    <a href="../auth/logout.php?type=admin" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </header>
            <div class="admin-content">
                <?php include "pages/{$page}.php"; ?>
            </div>
        </main>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.getElementById('themeIcon').className = savedTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const newTheme = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            document.getElementById('themeIcon').className = newTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        }
    </script>
</body>
</html>

