@extends('layouts.main')

@section('title', 'Edit Laporan')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Laporan</h1>
    <div class="card p-4">
        <form action="{{ route('laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="Foto" class="form-label">Tambah Foto Baru (Total maks. 5 foto):</label>
                <input type="file" class="form-control @error('Foto.*') is-invalid @enderror @error('Foto') is-invalid @enderror" id="Foto" name="Foto[]" multiple accept="image/*">
                @error('Foto.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('Foto')
                     <div class="invalid-feedback d-block">{{ $message }}</div>
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
                
                <!-- Container untuk preview foto yang baru diunggah -->
                <div id="new-photos-preview" class="mt-3 d-flex flex-wrap gap-2"></div>

                <div class="mt-3">
                    <label class="form-label">Foto Saat Ini (<span id="current-photo-count">{{ is_array($laporan->Foto) ? count($laporan->Foto) : 0 }}</span> foto):</label>
                    <div id="current-photos" class="d-flex flex-wrap gap-2">
                        @if($laporan->Foto && is_array($laporan->Foto))
                            @forelse($laporan->Foto as $foto)
                                <div class="position-relative current-photo-item">
                                    <img src="{{ url('images/' . $foto) }}" alt="Foto" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    <input type="hidden" name="existing_photos[]" value="{{ $foto }}">
                                    <button type="button" class="btn btn-danger btn-sm remove-photo" style="position:absolute; top:0; right:0; line-height:1; padding: 2px 6px;">&times;</button>
                                </div>
                            @empty
                                <p id="no-photo-text">Tidak ada foto yang diunggah.</p>
                            @endforelse
                        @else
                            <p id="no-photo-text">Tidak ada foto yang diunggah.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="departemen" class="form-label">Departemen:</label>
                <select class="form-select" id="departemen" name="departemen_supervisor_id" required>
                    <option value="">Pilih Departemen</option>
                    @foreach($departemens as $dept)
                        <option value="{{ $dept->id }}" {{ $laporan->departemen_supervisor_id == $dept->id ? 'selected' : '' }}>
                            {{ $dept->departemen }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="supervisor" class="form-label">Supervisor:</label>
                <input type="text" class="form-control" id="supervisor" value="{{ $laporan->departemenSupervisor->supervisor }}" readonly>
            </div>

            <div class="mb-3">
                <label for="kategori_masalah" class="form-label">Kategori Masalah:</label>
                <select class="form-select" id="kategori_masalah" name="kategori_masalah" required>
                    <option value="">Pilih Kategori Masalah</option>
                    <option value="Safety: Potensi bahaya" {{ $laporan->kategori_masalah == 'Safety: Potensi bahaya' ? 'selected' : '' }}>Safety: Potensi bahaya</option>
                    <option value="Seiri: Barang yang tidak diperlukan" {{ $laporan->kategori_masalah == 'Seiri: Barang yang tidak diperlukan' ? 'selected' : '' }}>Seiri: Barang yang tidak diperlukan</option>
                    <option value="Seiton: Barang tersusun dengan tidak rapi" {{ $laporan->kategori_masalah == 'Seiton: Barang tersusun dengan tidak rapi' ? 'selected' : '' }}>Seiton: Barang tersusun dengan tidak rapi</option>
                    <option value="Seiso: Kebersihan" {{ $laporan->kategori_masalah == 'Seiso: Kebersihan' ? 'selected' : '' }}>Seiso: Kebersihan</option>
                    <option value="Seiketsu: Tidak mengikuti SOP" {{ $laporan->kategori_masalah == 'Seiketsu: Tidak mengikuti SOP' ? 'selected' : '' }}>Seiketsu: Tidak mengikuti SOP</option>
                    <option value="Shitsuke: Evaluasi" {{ $laporan->kategori_masalah == 'Shitsuke: Evaluasi' ? 'selected' : '' }}>Shitsuke: Evaluasi</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="deskripsi_masalah" class="form-label">Deskripsi Masalah:</label>
                <textarea class="form-control" id="deskripsi_masalah" name="deskripsi_masalah" rows="3" required>{{ $laporan->deskripsi_masalah }}</textarea>
            </div>

            <div class="mb-3">
                <label for="tenggat_waktu" class="form-label">Tenggat Waktu:</label>
                <input type="date" class="form-control" id="tenggat_waktu" name="tenggat_waktu" value="{{ $laporan->tenggat_waktu }}" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Ditugaskan" {{ $laporan->status == 'Ditugaskan' ? 'selected' : '' }}>Ditugaskan</option>
                    <option value="Proses" {{ $laporan->status == 'Proses' ? 'selected' : '' }}>Proses</option>
                    <option value="Selesai" {{ $laporan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Update</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Elements ---
    const fotoInput = document.getElementById('Foto');
    const newPhotosPreview = document.getElementById('new-photos-preview');
    const currentPhotosContainer = document.getElementById('current-photos');
    const currentPhotoCountSpan = document.getElementById('current-photo-count');
    const noPhotoText = document.getElementById('no-photo-text');
    
    const openCameraBtn = document.getElementById('openCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');

    // --- State ---
    let newFileStore = []; // Stores new files from input and camera
    let stream = null;
    const MAX_TOTAL_PHOTOS = 5;

    // --- Core Functions ---
    function getTotalPhotoCount() {
        const existingPhotosCount = currentPhotosContainer.querySelectorAll('.current-photo-item').length;
        return existingPhotosCount + newFileStore.length;
    }

    function validateAndRender() {
        if (getTotalPhotoCount() > MAX_TOTAL_PHOTOS) {
            alert(`Jumlah total foto tidak boleh lebih dari ${MAX_TOTAL_PHOTOS}.`);
            // Trim excess files from the new file store
            const excessCount = getTotalPhotoCount() - MAX_TOTAL_PHOTOS;
            newFileStore.splice(newFileStore.length - excessCount, excessCount);
        }
        
        updateFileInput();
        renderNewPhotoPreviews();
        updateButtonsState();
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        newFileStore.forEach(file => dataTransfer.items.add(file));
        fotoInput.files = dataTransfer.files;
    }

    function renderNewPhotoPreviews() {
        newPhotosPreview.innerHTML = '';
        newFileStore.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative d-inline-block';
                wrapper.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 d-flex justify-content-center align-items-center" style="width:20px;height:20px;line-height:1;">&times;</button>
                `;
                wrapper.querySelector('button').onclick = () => {
                    newFileStore.splice(index, 1);
                    validateAndRender();
                };
                newPhotosPreview.appendChild(wrapper);
            }
            reader.readAsDataURL(file);
        });
    }

    function updateCurrentPhotoCount() {
        const count = currentPhotosContainer.querySelectorAll('.current-photo-item').length;
        if (currentPhotoCountSpan) currentPhotoCountSpan.innerText = count;
        if (noPhotoText) noPhotoText.style.display = count === 0 ? 'block' : 'none';
    }

    function updateButtonsState() {
        const canAddMore = getTotalPhotoCount() < MAX_TOTAL_PHOTOS;
        openCameraBtn.style.display = canAddMore ? 'inline-block' : 'none';
        if (!canAddMore) stopCamera();
    }

    // --- Event Listeners ---
    fotoInput.addEventListener('change', function(event) {
        const newFiles = Array.from(event.target.files);
        const cameraFiles = newFileStore.filter(f => f.name.startsWith('camera-'));
        newFileStore = [...cameraFiles, ...newFiles];
        validateAndRender();
    });

    currentPhotosContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-photo')) {
            event.target.closest('.current-photo-item').remove();
            updateCurrentPhotoCount();
            validateAndRender();
        }
    });

    // --- Camera Logic ---
    openCameraBtn.addEventListener('click', async function () {
        if (getTotalPhotoCount() >= MAX_TOTAL_PHOTOS) {
            alert(`Batas maksimal ${MAX_TOTAL_PHOTOS} foto tercapai.`);
            return;
        }
        cameraContainer.style.display = 'block';
        openCameraBtn.style.display = 'none';
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
        } catch (e) {
            stream = await navigator.mediaDevices.getUserMedia({ video: true }).catch(() => {
                alert('Tidak dapat mengakses kamera.');
                stopCamera();
            });
        }
        if (stream) video.srcObject = stream;
    });

    captureBtn.addEventListener('click', function () {
        if (getTotalPhotoCount() >= MAX_TOTAL_PHOTOS) {
            alert(`Batas maksimal ${MAX_TOTAL_PHOTOS} foto tercapai.`);
            stopCamera();
            return;
        }
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(function (blob) {
            const newFile = new File([blob], `camera-${Date.now()}.jpg`, { type: 'image/jpeg' });
            newFileStore.push(newFile);
            validateAndRender();
        }, 'image/jpeg', 0.95);
    });

    closeCameraBtn.addEventListener('click', stopCamera);

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
        cameraContainer.style.display = 'none';
        updateButtonsState();
    }

    // --- Initial Load ---
    updateCurrentPhotoCount();
    updateButtonsState();
});
</script>
@endpush