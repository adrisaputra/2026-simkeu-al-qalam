<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
    }
}
