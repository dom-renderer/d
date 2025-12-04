<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function inspectionLogs()
    {
        return $this->hasMany(JobInspectedLog::class);
    }
}
