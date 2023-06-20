<?php

namespace App\Models;

class ReportValueDetail extends Model
{
    protected $table = 'report_value_detail';

    public function student()
    {
        return $this->belongsTo(Person::class, 'student_id');
    }
}
