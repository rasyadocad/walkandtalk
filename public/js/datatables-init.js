$(document).ready(function() {
    if ($('#laporanTable').length) {
        var table = $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $('#laporanTable').data('url'),
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.error('DataTables error:', error);
                    alert('Gagal memuat data. Silakan refresh halaman.');
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'Tanggal', name: 'Tanggal' },
                { data: 'foto', name: 'foto', orderable: false, searchable: false },
                { data: 'departemen', name: 'departemenSupervisor.departemen' },
                { data: 'kategori_masalah', name: 'kategori_masalah', orderable: false },
                { data: 'deskripsi_masalah', name: 'deskripsi_masalah' },
                { data: 'tenggat_waktu', name: 'tenggat_waktu' },
                { data: 'status', name: 'status' },
                { data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false },
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

        // Refresh modal foto setelah data dimuat
        table.on('draw', function() {
            // Reinitialize modal triggers
            $('img[data-bs-toggle="modal"]').on('click', function() {
                var imgSrc = $(this).data('img-src');
                $('#imgPreviewFull').attr('src', imgSrc);
            });
        });
    }

    // DataTables untuk halaman sejarah
    if ($('#sejarahTable').length) {
        var ajaxUrl = $('#sejarahTable').data('url');
        $('#sejarahTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: ajaxUrl,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'Tanggal', name: 'Tanggal' },
                { data: 'foto', name: 'foto', orderable: false, searchable: false },
                { data: 'departemen', name: 'departemenSupervisor.departemen' },
                { data: 'kategori_masalah', name: 'kategori_masalah', orderable: false, searchable: true },
                { data: 'deskripsi_masalah', name: 'deskripsi_masalah' },
                { data: 'tenggat_waktu', name: 'tenggat_waktu' },
                { data: 'status', name: 'status', orderable: false, searchable: true },
                { data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false },
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
                infoEmpty: "Tidak ada data",
                infoFiltered: "(disaring dari _MAX_ data)",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            error: function (xhr, error, thrown) {
                alert('Gagal memuat data. Silakan refresh halaman.');
            }
        });

        // Modal preview foto tetap berfungsi
        $('#sejarahTable').on('click', 'img[data-bs-toggle="modal"]', function() {
            var src = $(this).attr('data-img-src');
            $('#imgPreviewFull').attr('src', src);
        });
    }

    // Modal dinamis untuk lihat tindakan
    $(document).on('click', '.lihat-tindakan-btn', function() {
        var id = $(this).data('id');
        $.get('/penyelesaian/' + id, function(res) {
            if (res.success) {
                var html = `
                    <div class="form-group mb-3">
                        <label>Tanggal Penyelesaian:</label>
                        <input type="text" class="form-control" value="${res.Tanggal}" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label>Foto Penyelesaian:</label>
                        ${res.Foto ? `<img src="${res.Foto}" alt="Foto Penyelesaian" class="img-fluid modal-photo">` : '<p class="text-muted">Tidak ada foto penyelesaian.</p>'}
                    </div>
                    <div class="form-group mb-3">
                        <label>Deskripsi Penyelesaian:</label>
                        <textarea class="form-control" rows="3" readonly>${res.deskripsi_penyelesaian}</textarea>
                    </div>
                `;
                $('#modalTindakanDinamisBody').html(html);
                $('#modalTindakanDinamis').modal('show');
            } else {
                $('#modalTindakanDinamisBody').html('<p class="text-danger">Data tidak ditemukan.</p>');
                $('#modalTindakanDinamis').modal('show');
            }
        });
    });

    // Row details untuk sejarah
    $(document).on('click', '.lihat-tindakan-row-btn', function() {
        var tr = $(this).closest('tr');
        var row = $('#sejarahTable').DataTable().row(tr);

        // Jika sudah terbuka, tutup
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            return;
        }

        // Tutup row lain yang terbuka
        $('#sejarahTable tbody tr.shown').each(function() {
            $('#sejarahTable').DataTable().row(this).child.hide();
            $(this).removeClass('shown');
        });

        // Ambil data via AJAX
        var id = $(this).data('id');
        $.get('/penyelesaian/' + id, function(res) {
            if (res.success) {
                var html = `
                    <div class="p-3">
                        <div><strong>Tanggal Penyelesaian:</strong> ${res.Tanggal}</div>
                        <div><strong>Foto Penyelesaian:</strong><br>
                            ${res.Foto ? `<img src="${res.Foto}" alt="Foto Penyelesaian" class="img-fluid modal-photo" style="max-width:200px;">` : '<span class="text-muted">Tidak ada foto penyelesaian.</span>'}
                        </div>
                        <div><strong>Deskripsi Penyelesaian:</strong><br>
                            <span>${res.deskripsi_penyelesaian}</span>
                        </div>
                    </div>
                `;
                row.child(html).show();
                tr.addClass('shown');
            } else {
                row.child('<div class="p-3 text-danger">Data tidak ditemukan.</div>').show();
                tr.addClass('shown');
            }
        });
    });
});