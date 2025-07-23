<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartemenSupervisor extends Model
{
    protected $table = 'departemen_supervisors';
    protected $fillable = ['departemen', 'supervisor', 'email', 'is_group', 'group_members'];

    protected $casts = [
        'is_group' => 'boolean',
        'group_members' => 'array'
    ];

    public function laporan()
    {
        return $this->hasMany(laporan::class, 'departemen_supervisor_id', 'id');
    }
}
