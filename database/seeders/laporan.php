<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class laporan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('laporan')->insert([
            [
                'Tanggal' => now(),
                'Foto' => 'public\images\laporan1.png',
                'Lokasi' => 'Gedung A',
                'Kategori Masalah' => 'Kebersihan',
                'Deskripsi Masalah' => 'Tumpukan sampah di area parkir.',
                'Tenggat Waktu' => '2025-05-15 17:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Tanggal' => now(),
                'Foto' => 'public\images\laporan2.jpg',
                'Lokasi' => 'Ruang Meeting 2',
                'Kategori Masalah' => 'Kerusakan',
                'Deskripsi Masalah' => 'Proyektor tidak berfungsi.',
                'Tenggat Waktu' => '2025-05-14 12:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Tanggal' => now(),
                'Foto' => 'public\images\laporan3.png',
                'Lokasi' => 'Kantin',
                'Kategori Masalah' => 'Keamanan',
                'Deskripsi Masalah' => 'Pintu darurat tidak bisa dibuka.',
                'Tenggat Waktu' => '2025-05-13 15:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Tanggal' => now(),
                'Foto' => 'public\images\laporan4.jpg',
                'Lokasi' => 'Lantai 3',
                'Kategori Masalah' => 'Listrik',
                'Deskripsi Masalah' => 'Lampu di lorong mati.',
                'Tenggat Waktu' => '2025-05-12 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Tanggal' => now(),
                'Foto' => 'public\images\laporan5.png',
                'Lokasi' => 'Area Parkir',
                'Kategori Masalah' => 'Kebersihan',
                'Deskripsi Masalah' => 'Genangan air di dekat pintu masuk.',
                'Tenggat Waktu' => '2025-05-11 18:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}