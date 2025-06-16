document.addEventListener('DOMContentLoaded', function() {
    // Toggle filter panel
    const filterPanel = document.querySelector('.filter-panel');
    const toggleFilterBtn = document.querySelector('.toggle-filter');
    const filterBody = document.querySelector('.filter-body');
    if (toggleFilterBtn && filterPanel && filterBody) {
        // Default: tampil (expanded) di desktop, tertutup di mobile
        function setInitialState() {
            if (window.innerWidth < 768) {
                filterPanel.classList.add('collapsed');
                toggleFilterBtn.querySelector('i').classList.remove('fa-chevron-up');
                toggleFilterBtn.querySelector('i').classList.add('fa-chevron-down');
                $(filterBody).hide();
            } else {
                filterPanel.classList.remove('collapsed');
                toggleFilterBtn.querySelector('i').classList.remove('fa-chevron-down');
                toggleFilterBtn.querySelector('i').classList.add('fa-chevron-up');
                $(filterBody).show();
            }
        }
        setInitialState();
        window.addEventListener('resize', setInitialState);

        toggleFilterBtn.addEventListener('click', function() {
            if (filterPanel.classList.contains('collapsed')) {
                filterPanel.classList.remove('collapsed');
                toggleFilterBtn.querySelector('i').classList.remove('fa-chevron-down');
                toggleFilterBtn.querySelector('i').classList.add('fa-chevron-up');
                $(filterBody).slideDown(200);
            } else {
                filterPanel.classList.add('collapsed');
                toggleFilterBtn.querySelector('i').classList.remove('fa-chevron-up');
                toggleFilterBtn.querySelector('i').classList.add('fa-chevron-down');
                $(filterBody).slideUp(200);
            }
        });
    }
    
    // Filter toggle functionality
    const resetFilterBtn = document.getElementById('resetFilter');
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function() {
            // Reset all filter controls except date range
            document.querySelectorAll('.filter-control').forEach(function(control) {
                if (control.id !== 'start_date' && control.id !== 'end_date') {
                    if (control.tagName === 'SELECT') {
                        control.selectedIndex = 0;
                    } else {
                        control.value = '';
                    }
                }
            });
            
            // Reset date ranges to default (last 30 days)
            if (startDateInput && endDateInput) {
                const today = new Date();
                endDateInput.value = today.toISOString().split('T')[0];
                
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(today.getDate() - 30);
                startDateInput.value = thirtyDaysAgo.toISOString().split('T')[0];
            }
            
            // Refresh table with cleared filters
            if (typeof refreshTable === 'function') {
                refreshTable();
            }
        });
    }
    
    // Date range default values (last 30 days)
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (startDateInput && endDateInput) {
        // Default end date to today
        const today = new Date();
        const endDateStr = today.toISOString().split('T')[0];
        endDateInput.value = endDateStr;
        
        // Default start date to 30 days ago
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(today.getDate() - 30);
        const startDateStr = thirtyDaysAgo.toISOString().split('T')[0];
        startDateInput.value = startDateStr;
    }
    
    // Apply filter button
    const applyFilterBtn = document.getElementById('applyFilter');
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', function() {
            // Get all filter values
            const filters = {
                start_date: document.getElementById('start_date')?.value || '',
                end_date: document.getElementById('end_date')?.value || '',
                departemen: document.getElementById('departemen')?.value || '',
                kategori: document.getElementById('kategori')?.value || '',
                status: document.getElementById('status')?.value || '',
                tenggat_bulan: document.getElementById('tenggat_bulan')?.value || ''
            };
            
            // Validate date range
            if (filters.start_date && filters.end_date) {
                if (new Date(filters.start_date) > new Date(filters.end_date)) {
                    alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                    return;
                }
            }
            
            // Refresh datatable with filters
            if (typeof refreshTable === 'function') {
                refreshTable(filters);
            }
        });
    }
    
    // Helper function to create filter badge HTML
    function createFilterBadge(label, value, filterType) {
        return `
            <span class="filter-badge">
                <span class="badge-label">${label}:</span>
                <span class="badge-value">${value}</span>
                <span class="badge-close" data-filter="${filterType}">
                    <i class="fas fa-times-circle"></i>
                </span>
            </span>
        `;
    }
    
    // Function to clear specific filter
    function clearSpecificFilter(filterType) {
        switch(filterType) {
            case 'date':
                // Reset date to last 30 days
                if (startDateInput && endDateInput) {
                    const today = new Date();
                    endDateInput.value = today.toISOString().split('T')[0];
                    
                    const thirtyDaysAgo = new Date();
                    thirtyDaysAgo.setDate(today.getDate() - 30);
                    startDateInput.value = thirtyDaysAgo.toISOString().split('T')[0];
                }
                break;
            case 'departemen':
                if (document.getElementById('departemen')) {
                    document.getElementById('departemen').selectedIndex = 0;
                }
                break;
            case 'kategori':
                if (document.getElementById('kategori')) {
                    document.getElementById('kategori').selectedIndex = 0;
                }
                break;
            case 'status':
                if (document.getElementById('status')) {
                    document.getElementById('status').selectedIndex = 0;
                }
                break;
            case 'tenggat_bulan':
                if (document.getElementById('tenggat_bulan')) {
                    document.getElementById('tenggat_bulan').selectedIndex = 0;
                }
                break;
        }
        
        // Refresh table with updated filters
        if (typeof refreshTable === 'function') {
            refreshTable();
        }
    }
});