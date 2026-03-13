<!-- Sidebar -->
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="main.php?page=dashboard" class="sidebar-brand">
            <img src="../../assets/img/ic2-nambertwo.png" alt="PTCI Logo" class="sidebar-logo" id="sidebarLogo">
            <div class="sidebar-brand-text">
                <span class="brand-main">PTCI</span>
                <span class="brand-sub">STELCOM Portal</span>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                <a href="dashboard" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item <?php echo ($page === 'votersAccount') ? 'active' : ''; ?>">
                <a href="voters-account" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Voters Log</span>
                </a>
            </li>

            <li class="nav-item <?php echo ($page === 'result') ? 'active' : ''; ?>">
                <a href="results" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Results</span>
                </a>
            </li>

        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar-row">
                <div class="admin-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="admin-details">
                    <span class="admin-name">STELCOM</span>
                    <span class="admin-role">STELCOM</span>
                </div>
        </div>
</aside>
