@component('mail::message')
# Laporan Baru Ditugaskan

Halo **{{ $laporan->departemenSupervisor->supervisor ?? '-' }}**,

Anda telah ditugaskan untuk menangani laporan berikut:

- **Kategori:** {{ $laporan->kategori_masalah }}
- **Deskripsi:** {{ $laporan->deskripsi_masalah }}
- **Tenggat Waktu:** {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('d-m-Y') }}

@component('mail::button', ['url' => url('/dashboard')])
Lihat Laporan
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent