@extends('layouts.main')

@section('title', 'Tambah Laporan')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Laporan</h1>
    <div class="card p-4">
        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="Foto" class="form-label">Foto:</label>
                <input type="file" class="form-control @error('Foto') is-invalid @enderror" id="Foto" name="Foto" accept="image/*">
                @error('Foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <button type="button" class="btn btn-secondary mt-2" id="openCameraBtn">Ambil Foto</button>
                <div id="cameraContainer" style="display:none; margin-top:10px;">
                    <video id="video" autoplay playsinline style="width:100%; max-width:350px; border:1px solid #ccc; border-radius:8px;"></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <div class="mt-2">
                        <button type="button" class="btn btn-success" id="captureBtn">Gunakan Foto</button>
                        <button type="button" class="btn btn-danger" id="closeCameraBtn">Tutup Kamera</button>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="departemen" class="form-label">Departemen:</label>
                <select class="form-select @error('departemen_supervisor_id') is-invalid @enderror" id="departemen" name="departemen_supervisor_id" required>
                    <option value="">Pilih Departemen</option>
                    @foreach($departemens as $dept)
                        <option value="{{ $dept->id }}" {{ old('departemen_supervisor_id') == $dept->id ? 'selected' : '' }}>{{ $dept->departemen }}</option>
                    @endforeach
                </select>
                @error('departemen_supervisor_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="supervisor" class="form-label">Supervisor:</label>
                <input type="text" class="form-control" id="supervisor" name="supervisor" readonly>
            </div>

            <div class="mb-3">
                <label for="kategori_masalah" class="form-label">Kategori Masalah:</label>
                <select class="form-select @error('kategori_masalah') is-invalid @enderror" id="kategori_masalah" name="kategori_masalah" required>
                    <option value="">Pilih Kategori Masalah</option>
                    <option value="Safety: Potensi bahaya">Safety: Potensi bahaya</option>
                    <option value="Seiri: Barang yang tidak diperlukan">Seiri: Barang yang tidak diperlukan</option>
                    <option value="Seiton: Barang tersusun dengan tidak rapi">Seiton: Barang tersusun dengan tidak rapi</option>
                    <option value="Seiso: Kebersihan">Seiso: Kebersihan</option>
                    <option value="Seiketsu: Tidak mengikuti SOP">Seiketsu: Tidak mengikuti SOP</option>
                    <option value="Shitsuke: Evaluasi">Shitsuke: Evaluasi</option>
                </select>
                @error('kategori_masalah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="deskripsi_masalah" class="form-label">Deskripsi Masalah:</label>
                <textarea class="form-control @error('deskripsi_masalah') is-invalid @enderror" id="deskripsi_masalah" name="deskripsi_masalah" rows="3" required>{{ old('deskripsi_masalah') }}</textarea>
                @error('deskripsi_masalah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tenggat_waktu" class="form-label">Tenggat Waktu:</label>
                <input type="date" class="form-control @error('tenggat_waktu') is-invalid @enderror" id="tenggat_waktu" name="tenggat_waktu" value="{{ old('tenggat_waktu') }}" required>
                @error('tenggat_waktu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-4">Submit</button>
        </form>
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
@endsection