document.addEventListener('DOMContentLoaded', function() {
    // Delegasi event untuk menangani tombol delete
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const deleteUrl = $(this).data('delete-url');
        const returnUrl = $(this).data('return-url');
        const id = $(this).data('id');
        const button = $(this);
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: "Apakah Anda yakin ingin menghapus laporan ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX DELETE request
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ref: returnUrl.includes('sejarah') ? 'sejarah' : 'dashboard'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload DataTable
                                $('.dataTable').DataTable().ajax.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        // Show error message
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat menghapus laporan.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});