<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\laporan;
use App\Models\Penyelesaian;
use App\Models\DepartemenSupervisor;
use Carbon\Carbon;
use Faker\Factory as Faker;

class LaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $departemenIds = DepartemenSupervisor::pluck('id')->toArray();
        $kategoriMasalah = [
            'Safety: Potensi bahaya',
            'Seiri: Barang yang tidak diperlukan',
            'Seiton: Barang tersusun dengan tidak rapi',
            'Seiso: Kebersihan',
            'Seiketsu: Tidak mengikuti SOP',
            'Shitsuke: Evaluasi'
        ];

        // Generate 50 laporan Ditugaskan
        for ($i = 0; $i < 50; $i++) {
            laporan::create([
                'Tanggal' => $faker->dateTimeBetween('-3 months', 'now'),
                'departemen_supervisor_id' => $faker->randomElement($departemenIds),
                'kategori_masalah' => $faker->randomElement($kategoriMasalah),
                'deskripsi_masalah' => $faker->paragraph(),
                'tenggat_waktu' => $faker->dateTimeBetween('now', '+1 month'),
                'status' => 'Ditugaskan',
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                'updated_at' => now()
            ]);
        }

        // Generate 50 laporan Proses
        for ($i = 0; $i < 50; $i++) {
            laporan::create([
                'Tanggal' => $faker->dateTimeBetween('-3 months', 'now'),
                'departemen_supervisor_id' => $faker->randomElement($departemenIds),
                'kategori_masalah' => $faker->randomElement($kategoriMasalah),
                'deskripsi_masalah' => $faker->paragraph(),
                'tenggat_waktu' => $faker->dateTimeBetween('now', '+1 month'),
                'status' => 'Proses',
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                'updated_at' => now()
            ]);
        }

        // Generate 50 laporan Selesai dengan Penyelesaian
        for ($i = 0; $i < 50; $i++) {
            $laporan = laporan::create([
                'Tanggal' => $faker->dateTimeBetween('-3 months', 'now'),
                'departemen_supervisor_id' => $faker->randomElement($departemenIds),
                'kategori_masalah' => $faker->randomElement($kategoriMasalah),
                'deskripsi_masalah' => $faker->paragraph(),
                'tenggat_waktu' => $faker->dateTimeBetween('now', '+1 month'),
                'status' => 'Selesai',
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                'updated_at' => now()
            ]);

            // Buat penyelesaian untuk setiap laporan selesai
            Penyelesaian::create([
                'laporan_id' => $laporan->id,
                'Tanggal' => $faker->dateTimeBetween($laporan->created_at, 'now'),
                'deskripsi_penyelesaian' => $faker->paragraph(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
