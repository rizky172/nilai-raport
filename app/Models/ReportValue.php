<?php

namespace App\Models;

class ReportValue extends Model
{
    protected $table = 'report_value';

    public function details()
    {
        return $this->hasMany(ReportValueDetail::class);
    }
}
