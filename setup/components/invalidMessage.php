<?php
function renderInvalidOverlay() {
?>
<div class="status-overlay" id="invalidOverlay">
    <div class="status-card">
        <div class="status-icon invalid">
            <i class="fas fa-times"></i>
        </div>
        <div class="status-title invalid" id="invalidTitle">Invalid!</div>
        <div class="status-subtitle" id="invalidSubtitle">Something went wrong.</div>
        <button class="status-btn" onclick="hideInvalidOverlay()">Try Again</button>
    </div>
</div>

<script>
    function showInvalidOverlay(title, subtitle) {
        document.getElementById('invalidTitle').textContent = title || 'Invalid!';
        document.getElementById('invalidSubtitle').innerHTML = subtitle || 'Something went wrong.';
        document.getElementById('invalidOverlay').classList.add('show');
    }

    function hideInvalidOverlay() {
        document.getElementById('invalidOverlay').classList.remove('show');
    }
</script>
<?php
}
?>
