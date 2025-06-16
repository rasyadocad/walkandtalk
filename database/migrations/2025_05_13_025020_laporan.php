<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('laporan', function (Blueprint $table) {
        $table->id();
        $table->timestamp('Tanggal')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->string('Foto')->nullable();
        $table->unsignedBigInteger('departemen_supervisor_id');
        $table->string('kategori_masalah');
        $table->text('deskripsi_masalah');
        $table->date('tenggat_waktu');
        $table->enum('status', ['Ditugaskan', 'Proses', 'Selesai'])->default('Ditugaskan'); // Ubah tipe data ke enum
        $table->timestamps();

        $table->foreign('departemen_supervisor_id')
              ->references('id')
              ->on('departemen_supervisors')
              ->onDelete('cascade');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};