<?php


function renderLoginSuccessOverlay() {
?>
<div class="status-overlay" id="loginSuccessOverlay">
    <div class="status-card">
        <div class="confetti-container" id="confettiContainer"></div>
        
        <div class="status-icon success">
            <i class="fas fa-check"></i>
        </div>
        
        <div class="status-title success" id="loginSuccessTitle">Login Successful!</div>
        <div class="status-subtitle" id="loginSuccessSubtitle">Welcome back! Redirecting you to cast your vote...</div>
        
        <button class="status-btn" id="loginSuccessBtn" onclick="proceedToVote()">Go to Vote</button>
        
        <div class="auto-redirect">
            Redirecting in <span id="loginCountdown">3</span> seconds...
        </div>
    </div>
</div>

<script>
    let loginCountdownValue = 3;
    let loginCountdownInterval = null;

    function showLoginSuccessOverlay(title, subtitle, buttonText, redirectUrl) {
        document.getElementById('loginSuccessTitle').textContent = title || 'Login Successful!';
        document.getElementById('loginSuccessSubtitle').innerHTML = subtitle || 'Welcome back! Redirecting you to cast your vote...';
        
        const btn = document.getElementById('loginSuccessBtn');
        if (buttonText) {
            btn.textContent = buttonText;
        }
        
        if (redirectUrl) {
            btn.onclick = function() {
                if (loginCountdownInterval) {
                    clearInterval(loginCountdownInterval);
                }
                window.location.href = redirectUrl;
            };
        }
        
        // Generate confetti
        const container = document.getElementById('confettiContainer');
        container.innerHTML = '';
        const colors = ['#222', '#444', '#666', '#888', '#aaa'];
        
        for (let i = 0; i < 30; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 2 + 's';
            confetti.style.width = (Math.random() * 8 + 6) + 'px';
            confetti.style.height = (Math.random() * 8 + 6) + 'px';
            confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
            container.appendChild(confetti);
        }
        
        document.getElementById('loginSuccessOverlay').classList.add('show');
        
        // Start countdown
        startLoginCountdown(redirectUrl);
    }
    
    function startLoginCountdown(redirectUrl) {
        loginCountdownValue = 3;
        document.getElementById('loginCountdown').textContent = loginCountdownValue;
        
        if (loginCountdownInterval) {
            clearInterval(loginCountdownInterval);
        }
        
        loginCountdownInterval = setInterval(function() {
            loginCountdownValue--;
            document.getElementById('loginCountdown').textContent = loginCountdownValue;
            
            if (loginCountdownValue <= 0) {
                clearInterval(loginCountdownInterval);
                window.location.href = redirectUrl || '../public/pages/vote.php';
            }
        }, 1000);
    }
    
    function proceedToVote() {
        if (loginCountdownInterval) {
            clearInterval(loginCountdownInterval);
        }
        window.location.href = '../public/pages/vote.php';
    }
    
    function hideLoginSuccessOverlay() {
        document.getElementById('loginSuccessOverlay').classList.remove('show');
        if (loginCountdownInterval) {
            clearInterval(loginCountdownInterval);
        }
    }
</script>
<?php
}
?>

