<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penyelesaian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penyelesaian';

    protected $fillable = [
        'laporan_id',
        'Tanggal',
        'Foto',
        'deskripsi_penyelesaian',
    ];

    public function laporan()
    {
        return $this->belongsTo(\App\Models\laporan::class, 'laporan_id');
    }
}
