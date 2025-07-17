@extends('layouts.main')

@section('title', 'Tambah Laporan')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Laporan</h1>
    <div class="card p-4">
        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="Foto" class="form-label">Foto (Maks. 5 foto):</label>
                <input type="file" class="form-control @error('Foto.*') is-invalid @enderror" id="Foto" name="Foto[]" accept="image/*" multiple>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Elements ---
    const fotoInput = document.getElementById('Foto');
    const previewContainer = document.getElementById('foto-preview-container');
    const openCameraBtn = document.getElementById('openCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');
    
    // --- State ---
    let fileStore = []; // The single source of truth for all files
    let stream = null;
    const MAX_FILES = 5;

    // --- File Input Handling ---
    fotoInput.addEventListener('change', function(event) {
        const newFiles = Array.from(event.target.files);
        // When user selects files, it replaces existing ones from the input,
        // so we combine our camera files with the new selection.
        const cameraFiles = fileStore.filter(f => f.name.startsWith('camera-'));
        addFiles([...cameraFiles, ...newFiles], true);
    });

    // --- Camera Handling ---
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
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
            } catch (err) {
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin.');
                stopCamera();
                return;
            }
        }
        video.srcObject = stream;
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
            const newFile = new File([blob], `camera-${Date.now()}.jpg`, { type: 'image/jpeg' });
            addFiles([newFile]); // Add camera photo to fileStore
        }, 'image/jpeg', 0.95);
        
        // Keep camera open for more photos if limit not reached
        if (fileStore.length + 1 >= MAX_FILES) {
            stopCamera();
        }
    });

    closeCameraBtn.addEventListener('click', stopCamera);

    // --- Helper Functions ---
    function addFiles(newFiles, isReplacement = false) {
        let combined = isReplacement ? newFiles : [...fileStore, ...newFiles];

        if (combined.length > MAX_FILES) {
            alert(`Anda hanya dapat mengunggah maksimal ${MAX_FILES} foto.`);
            // Trim the excess files
            combined = combined.slice(0, MAX_FILES);
        }
        
        fileStore = combined;
        updateFileInput();
        renderPreviews();

        if (fileStore.length >= MAX_FILES) {
            openCameraBtn.style.display = 'none';
            stopCamera();
        } else {
            openCameraBtn.style.display = 'inline-block';
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
        cameraContainer.style.display = 'none';
        if (fileStore.length < MAX_FILES) {
            openCameraBtn.style.display = 'inline-block';
        }
    }

    function renderPreviews() {
        previewContainer.innerHTML = '';
        fileStore.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewWrapper = document.createElement('div');
                previewWrapper.className = 'position-relative d-inline-block';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.className = 'img-thumbnail';

                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '&times;';
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 d-flex justify-content-center align-items-center';
                removeBtn.style.width = '20px';
                removeBtn.style.height = '20px';
                removeBtn.style.lineHeight = '1';
                removeBtn.onclick = function() {
                    fileStore.splice(index, 1);
                    updateFileInput();
                    renderPreviews();
                     if (fileStore.length < MAX_FILES) {
                        openCameraBtn.style.display = 'inline-block';
                    }
                };

                previewWrapper.appendChild(img);
                previewWrapper.appendChild(removeBtn);
                previewContainer.appendChild(previewWrapper);
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