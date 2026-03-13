<?php
function renderAdminInvalidOverlay() {
?>
<div class="admin-toast-container" id="adminInvalidToastContainer"></div>

<script>
    function showAdminInvalid(title, message, duration = 4000) {
        const container = document.getElementById('adminInvalidToastContainer');
        
        const toast = document.createElement('div');
        toast.className = 'admin-toast error';
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
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
    max-width: 380px;
}

.admin-toast {
    display: flex;
    align-items: flex-start;
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

.admin-toast.error {
    border-left: 4px solid #ef4444;
    background: #fef2f2;
}

.admin-toast.error .toast-icon {
    color: #ef4444;
    font-size: 18px;
    flex-shrink: 0;
    margin-top: 2px;
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-size: 14px;
    font-weight: 600;
    color: #991b1b;
    margin-bottom: 2px;
}

.toast-message {
    font-size: 13px;
    color: #b91c1c;
}

.toast-close {
    background: none;
    border: none;
    color: #dc2626;
    cursor: pointer;
    padding: 4px;
    font-size: 12px;
    transition: color 0.2s;
    flex-shrink: 0;
}

.toast-close:hover {
    color: #991b1b;
}

[data-theme="dark"] .admin-toast.error {
    background: #1f1212;
}

[data-theme="dark"] .toast-title {
    color: #fca5a5;
}

[data-theme="dark"] .toast-message {
    color: #f87171;
}

[data-theme="dark"] .toast-close {
    color: #f87171;
}

[data-theme="dark"] .toast-close:hover {
    color: #fca5a5;
}
</style>
<?php
}
?>
