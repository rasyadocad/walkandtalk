<div class="filter-panel card mb-4 position-relative">
    <button type="button" class="btn-close position-absolute end-0 top-0 m-3" aria-label="Close" id="closeFilterPanelBtn" style="z-index:10; display:none;"></button>
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Filter Data
        </h5>
        <button class="btn btn-sm btn-link p-0 toggle-filter" type="button">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="card-body filter-body">
        <form id="filterForm">
            <div class="row">
                <!-- Rentang Tanggal -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <label class="form-label">Tanggal Laporan</label>
                    <div class="input-group">
                        <input type="date" class="form-control filter-control" id="start_date" name="start_date">
                        <span class="input-group-text">s/d</span>
                        <input type="date" class="form-control filter-control" id="end_date" name="end_date">
                    </div>
                </div>

                <!-- Departemen -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <label class="form-label">Departemen</label>
                    <select class="form-select filter-control" id="departemen" name="departemen">
                        <option value="">Semua Departemen</option>
                        @foreach($departemens as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->departemen }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori Masalah -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <label class="form-label">Kategori Masalah</label>
                    <select class="form-select filter-control" id="kategori" name="kategori">
                        <option value="">Semua Kategori</option>
                        <option value="Safety: Potensi bahaya">Safety: Potensi bahaya</option>
                        <option value="Seiri: Barang yang tidak diperlukan">Seiri: Barang yang tidak diperlukan</option>
                        <option value="Seiton: Barang tersusun dengan tidak rapi">Seiton: Barang tersusun dengan tidak rapi</option>
                        <option value="Seiso: Kebersihan">Seiso: Kebersihan</option>
                        <option value="Seiketsu: Tidak mengikuti SOP">Seiketsu: Tidak mengikuti SOP</option>
                        <option value="Shitsuke: Evaluasi">Shitsuke: Evaluasi</option>
                    </select>
                </div>

                <!-- Status (hanya untuk dashboard) -->
                @if(isset($showStatusFilter) && $showStatusFilter)
                <div class="col-md-6 col-lg-3 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select filter-control" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="Ditugaskan">Ditugaskan</option>
                        <option value="Proses">Proses</option>
                        @if(isset($includeSelesai) && $includeSelesai)
                        <option value="Selesai">Selesai</option>
                        @endif
                    </select>
                </div>
                @endif

                <!-- Tenggat Waktu -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <label class="form-label">Tenggat Waktu</label>
                    <select class="form-select filter-control" id="tenggat_bulan" name="tenggat_bulan">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-2">
                <button type="button" class="btn btn-secondary" id="resetFilter">
                    <i class="fas fa-undo-alt me-1"></i>Reset
                </button>
                <button type="button" class="btn btn-primary" id="applyFilter">
                    <i class="fas fa-search me-1"></i>Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>