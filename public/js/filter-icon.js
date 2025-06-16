document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.getElementById('filterIconBtn');
    const filterPanel = document.getElementById('floatingFilterPanel');
    const closeBtn = document.getElementById('closeFilterPanelBtn');

    // Overlay for mobile (to close panel when clicking outside)
    let overlay = document.querySelector('.floating-filter-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'floating-filter-overlay';
        document.body.appendChild(overlay);
    }

    function openPanel() {
        filterPanel.style.display = 'block';
        overlay.style.display = 'block';
        if (closeBtn) closeBtn.style.display = 'block';
        // Scroll to top if on mobile
        if (window.innerWidth < 600) window.scrollTo({top: 0, behavior: 'smooth'});
    }
    function closePanel() {
        filterPanel.style.display = 'none';
        overlay.style.display = 'none';
        if (closeBtn) closeBtn.style.display = 'none';
    }

    if (filterBtn && filterPanel) {
        filterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (filterPanel.style.display === 'block') {
                closePanel();
            } else {
                openPanel();
            }
        });
        overlay.addEventListener('click', closePanel);
        // Optional: close when clicking outside
        document.addEventListener('click', function(e) {
            if (!filterPanel.contains(e.target) && e.target !== filterBtn) {
                closePanel();
            }
        });
        if (closeBtn) {
            closeBtn.addEventListener('click', closePanel);
        }
    }

    // Ensure filter button is correctly positioned
    function ensureFilterButton() {
        var filterBtn = $('#filterIconBtn');
        if (filterBtn.length && $('.dataTables_filter').length) {
            // Jika tombol belum ada di dataTables_filter, pindahkan
            if (!$.contains($('.dataTables_filter')[0], filterBtn[0])) {
                $('.dataTables_filter').prepend(filterBtn.show());
            } else {
                filterBtn.show();
            }
        }
    }

    ensureFilterButton();
});