<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            // Mengubah kolom Foto menjadi TEXT untuk menampung lebih banyak data
            $table->text('Foto')->nullable()->change();
        });

        Schema::table('penyelesaian', function (Blueprint $table) {
            // Mengubah kolom Foto menjadi TEXT juga di tabel penyelesaian
            $table->text('Foto')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            // Kembalikan ke string jika diperlukan (sesuaikan panjangnya)
            $table->string('Foto', 255)->nullable()->change();
        });

        Schema::table('penyelesaian', function (Blueprint $table) {
            $table->string('Foto', 255)->nullable()->change();
        });
    }
};
