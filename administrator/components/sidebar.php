<!-- Sidebar -->
<aside class="admin-sidebar">
    <div class="sidebar-header">
<a href="./dashboard" class="sidebar-brand">
            <img src="../../assets/img/ic2-nambertwo.png" alt="PTCI Logo" class="sidebar-logo" id="sidebarLogo">
            <div class="sidebar-brand-text">
                <span class="brand-main">PTCI</span>
                <span class="brand-sub">Admin Portal</span>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                <a href="./dashboard" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($page === 'votersAccount') ? 'active' : ''; ?>">
                <a href="./votersAccount" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Voters Account</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($page === 'result') ? 'active' : ''; ?>">
                <a href="./result" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Results</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($page === 'setup') ? 'active' : ''; ?>">
                <a href="./setup" class="nav-link">
                    <i class="fas fa-cogs"></i>
                    <span>Setup</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($page === 'manage') ? 'active' : ''; ?>">
                <a href="./manage" class="nav-link">
                    <i class="fas fa-user-tie"></i>
                    <span>Manage Candidates</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($page === 'account') ? 'active' : ''; ?>">
                <a href="./account" class="nav-link">
                    <i class="fas fa-user-cog"></i>
                    <span>Account</span>
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
                    <span class="admin-name">Administrator</span>
                    <span class="admin-role">Admin</span>
                </div>
        </div>
</aside>
