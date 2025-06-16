<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departemen_supervisors', function (Blueprint $table) {
            $table->id();
            $table->string('departemen');
            $table->string('supervisor');
            $table->string('email')->nullable(); // HAPUS ->after('supervisor')
            $table->timestamps();
        });

        // Data awal departemen dan supervisor
        DB::table('departemen_supervisors')->insert([
            ['departemen' => 'LV Assembly', 'supervisor' => 'Aris Setiawan', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'LV Box', 'supervisor' => 'Rachmad Haryono', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'MV Assembly', 'supervisor' => 'Helmy Sundani', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'Prefabrication', 'supervisor' => 'Sarifudin Raysan', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'Packing', 'supervisor' => 'Bayu Putra Trianto', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'Tool Store', 'supervisor' => 'Joni Rahman', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'QC LV', 'supervisor' => 'Ishak Marthen', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'QC MV', 'supervisor' => 'Sirad Nova Mihardi', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'IQC', 'supervisor' => 'Abduh Al Agani', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'LV Module', 'supervisor' => 'Hadi Djohansyah', 'email' => 'sirjotaro666@gmail.com'],
            ['departemen' => 'Warehouse', 'supervisor' => 'Suhendra', 'email' => 'sirjotaro666@gmail.com']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departemen_supervisors');
    }
};
