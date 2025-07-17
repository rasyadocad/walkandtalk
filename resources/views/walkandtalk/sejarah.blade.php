@extends('layouts.main')

@section('title', 'Sejarah Laporan')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="mt-4">Sejarah Laporan</h1>
        <a href="{{ route('sejarah.download') }}" class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Download Laporan
        </a>
    </div>

    <!-- Filter Panel -->
    <div class="mb-3">
        @include('components.filter-panel', ['departemens' => $departemens, 'showStatusFilter' => false])
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table id="sejarahTable" class="table table-bordered table-striped w-100" data-url="{{ route('sejarah.datatables') }}">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Foto</th>
                        <th>Departemen</th>
                        <th>Kategori Masalah</th>
                        <th>Deskripsi Masalah</th>
                        <th>Tenggat Waktu</th>
                        <th>Status</th>
                        <th>Penyelesaian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi oleh DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview Foto Full -->
<div class="modal fade" id="modalFotoFull" tabindex="-1" aria-labelledby="modalFotoFullLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center p-0">
        <div id="photoCarousel" class="carousel slide">
          <div class="carousel-inner">
            <!-- Carousel items will be injected here -->
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Penyelesaian -->
<div class="modal fade" id="modalPenyelesaian" tabindex="-1" aria-labelledby="modalPenyelesaianLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPenyelesaianLabel">Detail Penyelesaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalPenyelesaianBody">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
    let errorMessages = @json($errors->all());
    let formattedErrors = errorMessages.map(msg => `• ${msg}`).join('<br>');
    let toastBody = document.getElementById('mainToastBody');
    let toastEl = document.getElementById('mainToast');
    let toastIcon = document.getElementById('mainToastIcon');
    if (toastBody && toastEl && toastIcon) {
        toastBody.innerHTML = formattedErrors;
        toastEl.classList.remove('bg-success', 'bg-info', 'bg-warning');
        toastEl.classList.add('bg-danger');
        toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-danger fs-5"></i>';
        let toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>
@endif

@if (session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    let toastBody = document.getElementById('mainToastBody');
    let toastEl = document.getElementById('mainToast');
    let toastIcon = document.getElementById('mainToastIcon');
    if (toastBody && toastEl && toastIcon) {
        toastBody.innerHTML = '{{ session('success') }}';
        toastEl.classList.remove('bg-danger', 'bg-info', 'bg-warning');
        toastEl.classList.add('bg-success');
        toastIcon.innerHTML = '<i class="fas fa-check-circle text-success fs-5"></i>';
        let toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>
@endif

@if (session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    let pesan = `{!! session('error') !!}`;
    let toastBody = document.getElementById('mainToastBody');
    let toastEl = document.getElementById('mainToast');
    let toastIcon = document.getElementById('mainToastIcon');
    if (toastBody && toastEl && toastIcon) {
        toastBody.innerHTML = pesan;
        toastEl.classList.remove('bg-success', 'bg-info', 'bg-warning');
        toastEl.classList.add('bg-danger', 'text-white');
        toastIcon.innerHTML = `<i class="fas fa-times-circle text-white"></i>`;
    }
});
</script>
@endif
@endsection

@push('scripts')
<script>
function ensureFilterButton() {
    var filterBtn = $('#filterIconBtn');
    if (filterBtn.length && $('.dataTables_filter').length) {
        if (!$.contains($('.dataTables_filter')[0], filterBtn[0])) {
            $('.dataTables_filter').prepend(filterBtn.show());
        } else {
            filterBtn.show();
        }
    }
}

$(document).ready(function() {
    const originalUrl = $('#sejarahTable').data('url');
    
    window.refreshTable = function(filters = null) {
        if ($.fn.DataTable.isDataTable('#sejarahTable')) {
            $('#sejarahTable').DataTable().destroy();
        }
        let ajaxUrl = originalUrl;
        if (filters) {
            const params = new URLSearchParams();
            if (filters.start_date) params.append('start_date', filters.start_date);
            if (filters.end_date) params.append('end_date', filters.end_date);
            if (filters.departemen) params.append('departemen', filters.departemen);
            if (filters.kategori) params.append('kategori', filters.kategori);
            if (filters.tenggat_bulan) params.append('tenggat_bulan', filters.tenggat_bulan);
            ajaxUrl = `${originalUrl}?${params.toString()}`;
        }
        var table = $('#sejarahTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: ajaxUrl,
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'Tanggal', name: 'Tanggal'},
                {data: 'foto', name: 'foto', orderable: false, searchable: false},
                {data: 'departemen', name: 'departemenSupervisor.departemen'},
                {data: 'kategori_masalah', name: 'kategori_masalah'},
                {data: 'deskripsi_masalah', name: 'deskripsi_masalah'},
                {data: 'tenggat_waktu', name: 'tenggat_waktu'},
                {data: 'status', name: 'status'},
                {data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false},
                {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
            ],
            order: [[1, 'desc']],
            language: {
                processing: "Memuat...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data yang ditampilkan",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang sesuai pencarian",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            drawCallback: function(settings) {
                ensureFilterButton();
            }
        });
    };

    // Initial table load
    refreshTable();

    // Pindahkan tombol filter ke sebelum input "Cari:"
    ensureFilterButton();
    $('.dataTables_filter').addClass('d-flex align-items-center gap-2');
    $('.dataTables_filter label').addClass('mb-0');

    // === EVENT DELEGATION UNTUK MODAL ===

    // 1. Modal untuk Galeri Foto Masalah (Carousel)
    $(document).on('click', 'img[data-bs-toggle="modal"][data-bs-target="#modalFotoFull"]', function() {
        const photos = $(this).data('photos');
        const carouselInner = $('#photoCarousel .carousel-inner');
        carouselInner.empty(); // Hapus foto lama

        if (photos && Array.isArray(photos) && photos.length > 0) {
            photos.forEach((photoUrl, index) => {
                const activeClass = index === 0 ? 'active' : '';
                carouselInner.append(`
                    <div class="carousel-item ${activeClass}">
                        <img src="${photoUrl}" class="d-block w-100 img-fluid rounded shadow" style="max-height:80vh; object-fit: contain;" alt="Foto Laporan">
                    </div>
                `);
            });
        }
        $('#photoCarousel .carousel-control-prev, #photoCarousel .carousel-control-next').toggle(photos && photos.length > 1);
    });

    // 2. Modal untuk Detail Penyelesaian
    $(document).on('click', '.lihat-penyelesaian-btn', function() {
        var laporanId = $(this).data('id');
        var modalBody = $('#modalPenyelesaianBody');
        modalBody.html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
        
        $.get('/laporan/penyelesaian/' + laporanId, function(response) {
            if (response.success) {
                var html = '';
                if (response.Tanggal) {
                    html += '<div class="mb-3"><strong>Tanggal Selesai:</strong> ' + response.Tanggal + '</div>';
                }
                if (response.deskripsi_penyelesaian) {
                    html += '<div class="mb-3"><strong>Deskripsi:</strong><br><p class="mb-0">' + response.deskripsi_penyelesaian + '</p></div>';
                }
                
                // PERBAIKAN: Logika untuk menampilkan galeri foto penyelesaian
                html += '<strong>Foto Penyelesaian:</strong>';
                if (response.Foto && Array.isArray(response.Foto) && response.Foto.length > 0) {
                    html += '<div class="d-flex flex-wrap gap-2 mt-2">';
                    response.Foto.forEach(function(fotoUrl) {
                        // Tampilkan sebagai thumbnail yang bisa diklik untuk membuka di tab baru
                        html += `
                            <a href="${fotoUrl}" target="_blank" rel="noopener noreferrer" title="Lihat gambar penuh">
                                <img src="${fotoUrl}" alt="Foto Penyelesaian" class="img-thumbnail" style="width:120px; height:120px; object-fit:cover;">
                            </a>
                        `;
                    });
                    html += '</div>';
                } else {
                    html += '<p class="text-muted mt-1">Tidak ada foto penyelesaian yang diunggah.</p>';
                }
                modalBody.html(html);
            } else {
                modalBody.html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data penyelesaian untuk laporan ini.') + '</div>');
            }
        }).fail(function() {
            modalBody.html('<div class="alert alert-danger mb-0">Gagal mengambil data penyelesaian. Silakan coba lagi.</div>');
        });
    });
});
</script>
@endpush