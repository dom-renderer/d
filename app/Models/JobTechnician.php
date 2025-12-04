<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class JobTechnician extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function adb()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
