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
                <input type="file" class="form-control @error('Foto.*') is-invalid @enderror" id="Foto" name="Foto[]" multiple>
                @error('Foto.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('Foto')
                     <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                
                <div class="mt-3">
                    <label class="form-label">Foto Saat Ini:</label>
                    <div id="current-photos" class="d-flex flex-wrap gap-2">
                        @if($laporan->Foto && is_array($laporan->Foto))
                            @forelse($laporan->Foto as $foto)
                                <div class="position-relative">
                                    <img src="{{ url('images/' . $foto) }}" alt="Foto" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    <input type="hidden" name="existing_photos[]" value="{{ $foto }}">
                                    <button type="button" class="btn btn-danger btn-sm remove-photo" style="position:absolute; top:0; right:0;">&times;</button>
                                </div>
                            @empty
                                <p>Tidak ada foto yang diunggah.</p>
                            @endforelse
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
    const fotoInput = document.getElementById('Foto');
    const previewContainer = document.getElementById('foto-preview-container');
    const currentPhotosContainer = document.getElementById('current-photos');
    let files = [];
    const MAX_FILES = 5;

    fotoInput.addEventListener('change', function(event) {
        const newFiles = Array.from(event.target.files);
        
        if (newFiles.length > 0) {
            // Sembunyikan foto lama jika ada file baru yang dipilih
            currentPhotosContainer.style.display = 'none';
        } else {
            currentPhotosContainer.style.display = 'flex';
        }

        if (newFiles.length > MAX_FILES) {
            alert(`Anda hanya dapat mengunggah maksimal ${MAX_FILES} foto.`);
            fotoInput.value = ''; // Reset input
            previewContainer.innerHTML = ''; // Hapus pratinjau
            currentPhotosContainer.style.display = 'flex'; // Tampilkan lagi foto lama
            return;
        }
        
        files = newFiles;
        renderPreviews();
    });

    function renderPreviews() {
        previewContainer.innerHTML = '';
        files.forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.className = 'img-thumbnail';
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    }

    // Hapus foto yang ada
    document.querySelectorAll('.remove-photo').forEach(button => {
        button.addEventListener('click', function() {
            const photoContainer = this.closest('.position-relative');
            const fotoName = photoContainer.querySelector('input[type="hidden"]').value;

            // Hapus dari daftar foto yang ada
            currentPhotosContainer.removeChild(photoContainer);

            // Tambah input hidden untuk foto yang dihapus
            const deletedPhotosInput = document.getElementById('deleted-photos');
            if (deletedPhotosInput) {
                deletedPhotosInput.value += `${fotoName},`;
            } else {
                const newDeletedPhotosInput = document.createElement('input');
                newDeletedPhotosInput.type = 'hidden';
                newDeletedPhotosInput.name = 'deleted_photos';
                newDeletedPhotosInput.id = 'deleted-photos';
                newDeletedPhotosInput.value = `${fotoName},`;
                fotoInput.closest('form').appendChild(newDeletedPhotosInput);
            }
        });
    });
});
</script>
@endpush