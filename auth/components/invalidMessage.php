<?php

function renderLoginInvalidOverlay() {
?>
<div class="status-overlay" id="loginInvalidOverlay">
    <div class="status-card">
        <div class="status-icon invalid">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="status-title invalid" id="loginInvalidTitle">Login Failed!</div>
        <div class="status-subtitle" id="loginInvalidSubtitle">Something went wrong.</div>
        <button class="status-btn" onclick="hideLoginInvalidOverlay()">Try Again</button>
    </div>
</div>

<script>
    function showLoginInvalidOverlay(title, subtitle) {
        document.getElementById('loginInvalidTitle').textContent = title || 'Login Failed!';
        document.getElementById('loginInvalidSubtitle').innerHTML = subtitle || 'Something went wrong.';
        document.getElementById('loginInvalidOverlay').classList.add('show');
    }

    function hideLoginInvalidOverlay() {
        document.getElementById('loginInvalidOverlay').classList.remove('show');
        // Remove query parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // Check for error parameter on page load and show overlay
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        
        if (error) {
            let title = 'Login Failed!';
            let subtitle = '';
            
            switch(error) {
                case 'Account not found. Please register first':
                    subtitle = 'No account found with this Student ID. Please register first.';
                    break;
                case 'Your account is not yet verified. Please wait for admin approval':
                    title = 'Account Not Verified';
                    subtitle = 'Your account is still pending verification. Please wait for admin approval.';
                    break;
                case 'Invalid OTP. Please try again':
                    subtitle = 'The OTP you entered is incorrect. Please try again.';
                    break;
                case 'You have already cast your vote':
                    title = 'Already Voted';
                    subtitle = 'You have already cast your vote. Multiple votes are not allowed.';
                    break;
                default:
                    subtitle = error.replace(/\+/g, ' ');
            }
            
            showLoginInvalidOverlay(title, subtitle);
        }
    });
</script>
<?php
}
?>

