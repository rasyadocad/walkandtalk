<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class laporan extends Model
{
    use HasFactory, SoftDeletes;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'laporan';

    protected $fillable = [
        'Tanggal',
        'Foto',
        'departemen_supervisor_id',
        'kategori_masalah',
        'deskripsi_masalah',
        'tenggat_waktu',
        'status',
    ];

    public function penyelesaian()
    {
        return $this->hasOne(\App\Models\Penyelesaian::class, 'laporan_id');
    }

    public function departemenSupervisor()
    {
        return $this->belongsTo(DepartemenSupervisor::class, 'departemen_supervisor_id', 'id');
    }
}