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
                <label for="Foto" class="form-label">Foto:</label>
                <input type="file" class="form-control" id="Foto" name="Foto">
                @if($laporan->Foto)
                    <img src="{{ url('images/' . $laporan->Foto) }}" alt="Foto" class="img-thumbnail mt-2" style="width: 100px;">
                @endif
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