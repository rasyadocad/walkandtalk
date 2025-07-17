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
                <label for="Foto" class="form-label">Foto Penyelesaian (Maks. 5 foto):</label>
                <input type="file" class="form-control @error('Foto.*') is-invalid @enderror" id="Foto" name="Foto[]" multiple>
                @error('Foto.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="foto-preview-container" class="mt-2 d-flex flex-wrap gap-2"></div>
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

@push('scripts')
<script>
// Skrip kamera yang dimodifikasi untuk multi-foto
document.addEventListener('DOMContentLoaded', function () {
    const fotoInput = document.getElementById('Foto');
    const previewContainer = document.getElementById('foto-preview-container');
    const openCameraBtn = document.getElementById('openCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');
    
    let fileStore = [];
    let stream = null;
    const MAX_FILES = 5;

    if (!openCameraBtn) return;

    fotoInput.addEventListener('change', function(event) {
        const newFiles = Array.from(event.target.files);
        const cameraFiles = fileStore.filter(f => f.name.startsWith('penyelesaian-'));
        addFiles([...cameraFiles, ...newFiles], true);
    });

    openCameraBtn.addEventListener('click', async function () {
        if (fileStore.length >= MAX_FILES) {
            alert(`Anda sudah mencapai batas maksimal ${MAX_FILES} foto.`);
            return;
        }
        cameraContainer.style.display = 'block';
        openCameraBtn.style.display = 'none';
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
        } catch (e) {
            stream = await navigator.mediaDevices.getUserMedia({ video: true }).catch(err => {
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin.');
                stopCamera();
            });
        }
        if(stream) video.srcObject = stream;
    });

    captureBtn.addEventListener('click', function () {
        if (fileStore.length >= MAX_FILES) {
            alert(`Batas maksimal ${MAX_FILES} foto tercapai.`);
            stopCamera();
            return;
        }
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(function (blob) {
            const newFile = new File([blob], `penyelesaian-${Date.now()}.jpg`, { type: 'image/jpeg' });
            addFiles([newFile]);
        }, 'image/jpeg', 0.95);
        
        if (fileStore.length + 1 >= MAX_FILES) stopCamera();
    });

    closeCameraBtn.addEventListener('click', stopCamera);

    function addFiles(newFiles, isReplacement = false) {
        let combined = isReplacement ? newFiles : [...fileStore, ...newFiles];
        if (combined.length > MAX_FILES) {
            alert(`Anda hanya dapat mengunggah maksimal ${MAX_FILES} foto.`);
            combined = combined.slice(0, MAX_FILES);
        }
        fileStore = combined;
        updateFileInput();
        renderPreviews();
        openCameraBtn.style.display = fileStore.length < MAX_FILES ? 'inline-block' : 'none';
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
        cameraContainer.style.display = 'none';
        if (fileStore.length < MAX_FILES) openCameraBtn.style.display = 'inline-block';
    }

    function renderPreviews() {
        previewContainer.innerHTML = '';
        fileStore.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative d-inline-block';
                wrapper.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 d-flex justify-content-center align-items-center" style="width:20px;height:20px;line-height:1;">&times;</button>
                `;
                wrapper.querySelector('button').onclick = () => {
                    fileStore.splice(index, 1);
                    updateFileInput();
                    renderPreviews();
                    if (fileStore.length < MAX_FILES) openCameraBtn.style.display = 'inline-block';
                };
                previewContainer.appendChild(wrapper);
            }
            reader.readAsDataURL(file);
        });
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        fileStore.forEach(file => dataTransfer.items.add(file));
        fotoInput.files = dataTransfer.files;
    }
});
</script>
@endpush