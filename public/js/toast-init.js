document.addEventListener('DOMContentLoaded', function() {
    const toastEl = document.getElementById('mainToast');
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl, {
            delay: 5000,
            animation: true
        });
        
        // Auto show if has content
        const toastBody = document.getElementById('mainToastBody');
        if (toastBody && toastBody.innerHTML.trim() !== '') {
            // Format pesan error jika ada multiple errors
            if (toastBody.innerHTML.includes('<br>')) {
                let errors = toastBody.innerHTML.split('<br>');
                let formattedErrors = errors.map(err => `• ${err}`).join('<br>');
                toastBody.innerHTML = formattedErrors;
            }
            toast.show();
        }
    }
});