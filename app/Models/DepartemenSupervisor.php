<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartemenSupervisor extends Model
{
    protected $table = 'departemen_supervisors';
    protected $fillable = ['departemen', 'supervisor'];

    public function laporan()
    {
        return $this->hasMany(laporan::class, 'departemen_supervisor_id', 'id');
    }
}
