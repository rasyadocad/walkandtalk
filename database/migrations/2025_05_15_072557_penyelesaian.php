<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyelesaian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laporan_id'); // Relasi ke tabel laporan
            $table->timestamp('Tanggal')->nullable(); // Changed from date to timestamp
            $table->string('Foto')->nullable(); // Foto penyelesaian
            $table->text('deskripsi_penyelesaian'); // Deskripsi penyelesaian
            $table->timestamps();

            // Foreign key ke tabel laporan
            $table->foreign('laporan_id')->references('id')->on('laporan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyelesaian');
    }
};