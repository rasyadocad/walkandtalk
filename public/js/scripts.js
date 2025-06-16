document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const layoutSidenav = document.getElementById('layoutSidenav');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            layoutSidenav.classList.toggle('sb-sidenav-toggled');
        });
    }

    // Update datetime every second
    function updateDateTime() {
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        
        const now = new Date();
        const dateTimeString = now.toLocaleDateString('id-ID', options)
            .replace(/\./g, ':')  // Ganti titik dengan titik dua untuk format waktu
            .replace('pukul', ''); // Hapus kata "pukul" dari output
        document.querySelector('.datetime').textContent = dateTimeString;
    }

    // Update immediately and then every second
    if (document.querySelector('.datetime')) {
        updateDateTime();
        setInterval(updateDateTime, 1000);
    }

    var modalFoto = document.getElementById('modalFotoFull');
    var imgPreview = document.getElementById('imgPreviewFull');
    document.querySelectorAll('img[data-bs-toggle="modal"][data-bs-target="#modalFotoFull"]').forEach(function(img) {
        img.addEventListener('click', function() {
            var src = this.getAttribute('data-img-src');
            imgPreview.src = src;
        });
    });
    // Bersihkan src saat modal ditutup (opsional)
    if (modalFoto) {
        modalFoto.addEventListener('hidden.bs.modal', function () {
            imgPreview.src = '';
        });
    }
});