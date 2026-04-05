<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabs = document.querySelectorAll('.case-tab');
    const panels = document.querySelectorAll('.case-tab-panel');
    
    function switchTab(targetTabId) {
        const targetTab = document.querySelector(`.case-tab[data-tab="${targetTabId}"]`);
        const targetPanel = document.getElementById(targetTabId + '-panel');
        
        if (targetTab && targetPanel) {
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            targetTab.classList.add('active');
            targetPanel.classList.add('active');
            window.history.replaceState(null, null, '#' + targetTabId);
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTabId = this.getAttribute('data-tab');
            switchTab(targetTabId);
        });
    });

    const currentHash = window.location.hash.substring(1);
    if (currentHash) { switchTab(currentHash); }
    
    // Auto-refresh hearing data when hearing form is saved
    if (window.Livewire) {
        Livewire.on('hearingSaved', () => {
            setTimeout(() => {
                window.location.reload();
            }, 500);
        });
    }
});
</script>