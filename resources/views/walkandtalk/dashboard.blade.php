@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex align-items-center mt-4 mb-4 flex-wrap gap-2">
        <h1 class="mb-0">Dashboard</h1>
        <div class="mx-3 h1 mb-0 text-muted d-none d-md-block">|</div>
        <div class="datetime h1 mb-0"></div>
    </div>

    <!-- Filter Panel -->
    <div class="mb-3">
        @include('components.filter-panel', ['departemens' => $departemens, 'showStatusFilter' => true])
    </div>

    <!-- Statistik Cards -->
    <div class="dashboard-cards mb-4">
        <div class="stats-card card-blue">
            <h3>TOTAL LAPORAN</h3>
            <div class="number">{{ $totalLaporan }}</div>
        </div>
        <div class="stats-card card-yellow">
            <h3>LAPORAN DITUGASKAN</h3>
            <div class="number">{{ $laporanDitugaskan }}</div>
        </div>
        <div class="stats-card card-green">
            <h3>LAPORAN DIPROSES</h3>
            <div class="number">{{ $laporanDiproses }}</div>
        </div>
        <div class="stats-card card-red">
            <h3>LAPORAN SELESAI</h3>
            <div class="number">{{ $laporanSelesai }}</div>
        </div>
    </div>

    <!-- Table -->
    <div class="card p-3">
        <div class="table-responsive">
            <table id="laporanTable" class="table table-bordered table-striped w-100" data-url="{{ route('dashboard.datatables') }}">
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
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview Foto Full -->
<div class="modal fade" id="modalFotoFull" tabindex="-1" aria-labelledby="modalFotoFullLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center p-0">
        <img src="" id="imgPreviewFull" class="img-fluid rounded shadow" alt="Preview Foto" style="max-height:80vh;">
      </div>
    </div>
  </div>
</div>
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
    // Store the original URL
    const originalUrl = $('#laporanTable').data('url');
    // Refresh table with filters function
    window.refreshTable = function(filters = null) {
        if ($.fn.DataTable.isDataTable('#laporanTable')) {
            $('#laporanTable').DataTable().destroy();
        }
        let ajaxUrl = originalUrl;
        if (filters) {
            const params = new URLSearchParams();
            if (filters.start_date) params.append('start_date', filters.start_date);
            if (filters.end_date) params.append('end_date', filters.end_date);
            if (filters.departemen) params.append('departemen', filters.departemen);
            if (filters.kategori) params.append('kategori', filters.kategori);
            if (filters.status) params.append('status', filters.status);
            if (filters.tenggat_bulan) params.append('tenggat_bulan', filters.tenggat_bulan);
            ajaxUrl = `${originalUrl}?${params.toString()}`;
        }
        var table = $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: ajaxUrl,
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'Tanggal', name: 'Tanggal'},
                {data: 'foto', name: 'foto', orderable: false, searchable: false},
                {data: 'departemen', name: 'departemenSupervisor.departemen'},
                {data: 'kategori_masalah', name: 'kategori_masalah', orderable: false},
                {data: 'deskripsi_masalah', name: 'deskripsi_masalah'},
                {data: 'tenggat_waktu', name: 'tenggat_waktu'},
                {data: 'status', name: 'status'},
                {data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false},
                {
                    data: 'aksi', 
                    name: 'aksi', 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row, meta) {
                        let editBtn = `<a href="/edit${row.id}" class="btn btn-sm btn-warning me-1" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>`;
                        
                        let deleteBtn = `<button type="button" 
                            class="btn btn-sm btn-danger delete-btn"
                            data-id="${row.id}"
                            data-delete-url="/laporan/${row.id}/delete"
                            data-return-url="${window.location.pathname}"
                            title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>`;
                        
                        return editBtn + deleteBtn;
                    }
                }
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
            }
        });
        table.on('draw', function() {
            $('img[data-bs-toggle="modal"]').on('click', function() {
                var imgSrc = $(this).data('img-src');
                $('#imgPreviewFull').attr('src', imgSrc);
            });
            ensureFilterButton(); // <-- PENTING: panggil setiap draw
        });
    }
    // Initial table load
    refreshTable();

    // Pindahkan tombol filter ke sebelum input "Cari:"
    ensureFilterButton();
    $('.dataTables_filter').addClass('d-flex align-items-center gap-2');
    $('.dataTables_filter label').addClass('mb-0');
});
</script>
@endpush

<style>
.filter-body {
    padding: 1rem;
    transition: max-height 0.3s ease, padding 0.3s;
    overflow: hidden;
    max-height: 1000px;
}
.filter-panel.collapsed .filter-body {
    max-height: 0 !important;
    padding: 0 !important;
    overflow: hidden;
}
@media (max-width: 767.98px) {
    .filter-panel .filter-body {
        max-height: 0;
        padding: 0;
        display: none;
    }
    .filter-panel.expanded .filter-body {
        display: block;
        max-height: 1000px;
        padding: 1rem;
    }
}
</style>