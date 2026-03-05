<?php
/**
 * Success Message Component
 * 
 * Usage:
 * include 'successMessage.php';
 * showSuccessMessage($title, $subtitle, $buttonText, $buttonLink);
 * 
 * Or use the overlay directly with JavaScript:
 * showSuccessOverlay();
 */

function showSuccessMessage($title = "Success!", $subtitle = "Your action was completed successfully.", $buttonText = "Continue", $buttonLink = "#", $type = "success") {
?>
<!-- Success Message Overlay -->
<div class="success-message-overlay show" id="successOverlay">
    <div class="success-message-card <?php echo $type; ?>" id="successCard">
        <!-- Confetti Container -->
        <div class="confetti-container" id="confettiContainer"></div>
        
        <!-- Animated Checkmark -->
        <div class="success-checkmark">
            <div class="success-checkmark-circle">
                <svg viewBox="0 0 24 24">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
            </div>
        
        <!-- Success Text -->
        <div class="success-title"><?php echo htmlspecialchars($title); ?></div>
        <div class="success-subtitle"><?php echo htmlspecialchars($subtitle); ?></div>
        
        <!-- Button -->
        <a href="<?php echo htmlspecialchars($buttonLink); ?>" class="success-btn">
            <?php echo htmlspecialchars($buttonText); ?>
        </a>
    </div>

<script>
    // Generate confetti
    (function() {
        const container = document.getElementById('confettiContainer');
        const colors = ['#28a745', '#20c997', '#ffc107', '#17a2b8', '#6f42c1', '#e83e8c'];
        
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
    })();
</script>

<style>
    /* Ensure overlay shows by default when included */
    .success-message-overlay.show {
        opacity: 1;
        visibility: visible;
    }
</style>
<?php
}

/**
 * Render just the overlay HTML (for AJAX calls)
 */
function renderSuccessOverlay() {
?>
<div class="success-message-overlay" id="successOverlay">
    <div class="success-message-card" id="successCard">
        <div class="confetti-container" id="confettiContainer"></div>
        <div class="success-checkmark">
            <div class="success-checkmark-circle">
                <svg viewBox="0 0 24 24">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
            </div>
        <div class="success-title" id="successTitle">Success!</div>
        <div class="success-subtitle" id="successSubtitle">Your action was completed successfully.</div>
        <button class="success-btn" id="successBtn" onclick="hideSuccessOverlay()">Continue</button>
    </div>

<script>
    function showSuccessOverlay(title, subtitle, buttonText, redirectUrl) {
        document.getElementById('successTitle').textContent = title || 'Success!';
        document.getElementById('successSubtitle').textContent = subtitle || 'Your action was completed successfully.';
        
        const btn = document.getElementById('successBtn');
        if (buttonText) {
            btn.textContent = buttonText;
        }
        
        if (redirectUrl) {
            btn.onclick = function() {
                window.location.href = redirectUrl;
            };
        }
        
        // Generate confetti
        const container = document.getElementById('confettiContainer');
        container.innerHTML = '';
        const colors = ['#28a745', '#20c997', '#ffc107', '#17a2b8', '#6f42c1', '#e83e8c'];
        
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
    }
    
    function hideSuccessOverlay() {
        document.getElementById('successOverlay').classList.remove('show');
    }
</script>
<?php
}
?>
