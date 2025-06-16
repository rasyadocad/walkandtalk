@extends('layouts.main')

@section('title', 'Penyelesaian Laporan')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Penyelesaian Laporan</h1>
    <div class="card p-4">
        <form action="{{ route('laporan.storeTindakan', $laporan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="Tanggal" class="form-label">Tanggal Penyelesaian:</label>
                <input type="date" class="form-control @error('Tanggal') is-invalid @enderror" id="Tanggal" name="Tanggal" value="{{ old('Tanggal') }}">
                @error('Tanggal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="Foto" class="form-label">Foto Penyelesaian:</label>
                <input type="file" class="form-control @error('Foto') is-invalid @enderror" id="Foto" name="Foto">
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
                <label for="deskripsi_penyelesaian" class="form-label">Deskripsi Penyelesaian:</label>
                <textarea class="form-control @error('deskripsi_penyelesaian') is-invalid @enderror" id="deskripsi_penyelesaian" name="deskripsi_penyelesaian" rows="3">{{ old('deskripsi_penyelesaian') }}</textarea>
                @error('deskripsi_penyelesaian')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="Ditugaskan" {{ old('status', $laporan->status) == 'Ditugaskan' ? 'selected' : '' }}>Ditugaskan</option>
                    <option value="Proses" {{ old('status', $laporan->status) == 'Proses' ? 'selected' : '' }}>Proses</option>
                    <option value="Selesai" {{ old('status', $laporan->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary mt-4">Simpan</button>
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
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const tanggalInput = document.getElementById('Tanggal');
    const deskripsiInput = document.getElementById('deskripsi_penyelesaian');

    function updateRequiredFields() {
        if (statusSelect.value === 'Selesai') {
            tanggalInput.required = true;
            deskripsiInput.required = true;
        } else {
            tanggalInput.required = false;
            deskripsiInput.required = false;
        }
    }

    statusSelect.addEventListener('change', updateRequiredFields);
    updateRequiredFields(); // initial
});
</script>
@endpush