<?php
function renderSuccessOverlay() {
?>
<div class="status-overlay" id="successOverlay">
    <div class="status-card">
        <div class="confetti-container" id="confettiContainer"></div>
        
        <div class="status-icon success">
            <i class="fas fa-check"></i>
        </div>
        
        <div class="status-title success" id="successTitle">Success!</div>
        <div class="status-subtitle" id="successSubtitle">Your action was completed successfully.</div>
        
        <div class="otp-container" id="otpContainer" style="display: none;">
            <div class="otp-label">Your OTP</div>
            <div class="otp-value">
                <span id="otpDisplay"></span>
                <button type="button" class="otp-copy-btn" id="otpCopyBtn" onclick="copyOTP()">
                    <i class="fas fa-copy" id="copyIcon"></i>
                </button>
            </div>
            <div class="otp-copied" id="otpCopied">
                <i class="fas fa-check"></i> <span id="copiedText">Copied!</span>
            </div>
        </div>
        
        <button class="status-btn" id="successBtn" onclick="proceedToLogin()">Proceed to Login</button>
        
        <div class="auto-redirect">
            Redirecting in <span id="countdown">7</span> seconds...
        </div>
    </div>
</div>

<script>
    let countdownValue = 7;
    let countdownInterval = null;
    let currentOTP = '';

    function showSuccessOverlay(title, subtitle, buttonText, redirectUrl, otp = null) {
        document.getElementById('successTitle').textContent = title || 'Success!';
        document.getElementById('successSubtitle').innerHTML = subtitle || 'Your action was completed successfully.';
        
        const btn = document.getElementById('successBtn');
        if (buttonText) {
            btn.textContent = buttonText;
        }
        
        if (redirectUrl) {
            btn.onclick = function() {
                window.location.href = redirectUrl;
            };
        }
        
        // Show OTP if provided
        const otpContainer = document.getElementById('otpContainer');
        if (otp) {
            currentOTP = otp;
            document.getElementById('otpDisplay').textContent = otp;
            otpContainer.style.display = 'block';
        } else {
            otpContainer.style.display = 'none';
        }
        
        // Generate confetti
        const container = document.getElementById('confettiContainer');
        container.innerHTML = '';
        const colors = ['#222', '#444', '#666', '#888', '#aaa'];
        
        for (let i = 0; i < 50; i++) {
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
        
        document.getElementById('successOverlay').classList.add('show');
        
        // Start countdown
        startCountdown(redirectUrl);
    }
    
    function startCountdown(redirectUrl) {
        countdownValue = 7;
        document.getElementById('countdown').textContent = countdownValue;
        
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        
        countdownInterval = setInterval(function() {
            countdownValue--;
            document.getElementById('countdown').textContent = countdownValue;
            
            if (countdownValue <= 0) {
                clearInterval(countdownInterval);
                window.location.href = redirectUrl || '../../auth/form.php';
            }
        }, 1000);
    }
    
    function copyOTP() {
        if (!currentOTP) return;
        
        // Method 1: Try clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(currentOTP).then(function() {
                showCopiedFeedback();
            }).catch(function() {
                fallbackCopy(currentOTP);
            });
        } else {
            fallbackCopy(currentOTP);
        }
    }
    
    function fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            document.body.removeChild(textArea);
            
            if (successful) {
                showCopiedFeedback();
            } else {
                alert('Unable to copy. Please manually select and copy: ' + currentOTP);
            }
        } catch (err) {
            document.body.removeChild(textArea);
            alert('Unable to copy. Please manually select and copy: ' + currentOTP);
        }
    }
    
    function showCopiedFeedback() {
        const copiedEl = document.getElementById('otpCopied');
        const iconEl = document.getElementById('copyIcon');
        
        copiedEl.classList.add('show');
        iconEl.className = 'fas fa-check';
        
        setTimeout(function() {
            copiedEl.classList.remove('show');
            iconEl.className = 'fas fa-copy';
        }, 2000);
    }
    
    function proceedToLogin() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        window.location.href = '../../auth/form.php';
    }
    
    function hideSuccessOverlay() {
        document.getElementById('successOverlay').classList.remove('show');
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
    }
</script>
<?php
}
?>
