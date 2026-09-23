function openTab(id, el) {
    // Hide all tab content
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.style.display = 'none';
    });

    // Show the selected tab content
    document.getElementById(id).style.display = 'block';

    // Remove .active from ALL tab triggers (no matter what they are)
    document.querySelectorAll('[data-tab-trigger]').forEach(trigger => {
        trigger.classList.remove('active');
    });

    // Add active class to clicked item
    el.classList.add('active');
}
