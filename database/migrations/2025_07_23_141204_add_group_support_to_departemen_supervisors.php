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
        Schema::table('departemen_supervisors', function (Blueprint $table) {
            $table->boolean('is_group')->default(false);
            $table->json('group_members')->nullable();
        });

        // Add the group entries
        DB::table('departemen_supervisors')->insert([
            [
                'departemen' => 'LV',
                'supervisor' => 'Grouped: LV Assembly, LV Box, LV Module',
                'email' => '', // Will send to multiple emails via group_members
                'is_group' => true,
                'group_members' => json_encode([
                    ['name' => 'LV Assembly', 'supervisor' => 'Aris Setiawan', 'email' => 'sirjotaro666@gmail.com'],
                    ['name' => 'LV Box', 'supervisor' => 'Rachmad Haryono', 'email' => 'sirjotaro666@gmail.com'],
                    ['name' => 'LV Module', 'supervisor' => 'Hadi Djohansyah', 'email' => 'sirjotaro666@gmail.com']
                ])
            ],
            [
                'departemen' => 'QC',
                'supervisor' => 'Grouped: QC MV, QC LV',
                'email' => '', // Will send to multiple emails via group_members
                'is_group' => true,
                'group_members' => json_encode([
                    ['name' => 'QC MV', 'supervisor' => 'Ishak Marthen', 'email' => 'sirjotaro666@gmail.com'],
                    ['name' => 'QC LV', 'supervisor' => 'Sirad Nova Mihardi', 'email' => 'sirjotaro666@gmail.com']
                ])
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the group entries
        DB::table('departemen_supervisors')
            ->where('is_group', true)
            ->delete();

        Schema::table('departemen_supervisors', function (Blueprint $table) {
            $table->dropColumn('is_group');
            $table->dropColumn('group_members');
        });
    }
};
