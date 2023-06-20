<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    protected $table = 'person';

    public function category()
    {
        return $this->belongsTo(Category::class, 'person_category_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'person_id');
    }

    public function class()
    {
        return $this->meta()
                ->where('key', 'class_id');
    }

    public function major()
    {
        return $this->meta()
                ->where('key', 'major_id');
    }

    public function lesson()
    {
        return $this->meta()
                ->where('key', 'lesson_id');
    }
}
