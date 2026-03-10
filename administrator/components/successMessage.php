<?php
function renderAdminSuccessOverlay() {
?>
<div class="admin-toast-container" id="adminToastContainer"></div>

<script>
    function showAdminSuccess(message, duration = 3000) {
        const container = document.getElementById('adminToastContainer');
        
        const toast = document.createElement('div');
        toast.className = 'admin-toast success';
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Auto remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    function showAdminError(message, duration = 4000) {
        const container = document.getElementById('adminToastContainer');
        
        const toast = document.createElement('div');
        toast.className = 'admin-toast error';
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Auto remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // Auto-show from URL params
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.get('success') === 'true') {
            showAdminSuccess('Operation completed successfully!');
        }
        
        if (urlParams.get('error')) {
            showAdminError(decodeURIComponent(urlParams.get('error')));
        }
    });
</script>

<style>
.admin-toast-container {
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 360px;
}

.admin-toast {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: var(--bg-card, #fff);
    border: 1px solid var(--border, #e0e0e0);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateX(120%);
    transition: transform 0.3s ease;
}

.admin-toast.show {
    transform: translateX(0);
}

.admin-toast.success {
    border-left: 4px solid #22c55e;
}

.admin-toast.success .toast-icon {
    color: #22c55e;
}

.admin-toast.error {
    border-left: 4px solid #ef4444;
}

.admin-toast.error .toast-icon {
    color: #ef4444;
}

.toast-icon {
    font-size: 18px;
    flex-shrink: 0;
}

.toast-message {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary, #333);
}

.toast-close {
    background: none;
    border: none;
    color: var(--text-muted, #999);
    cursor: pointer;
    padding: 4px;
    font-size: 12px;
    transition: color 0.2s;
}

.toast-close:hover {
    color: var(--text-primary, #333);
}

[data-theme="dark"] .admin-toast {
    background: #1a1a1a;
    border-color: #333;
}

[data-theme="dark"] .toast-message {
    color: #e0e0e0;
}

[data-theme="dark"] .toast-close {
    color: #666;
}

[data-theme="dark"] .toast-close:hover {
    color: #fff;
}
</style>
<?php
}
?>
