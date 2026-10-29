<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'classes_id',
        'nisn',
        'nis',
        'nipd',
        'name',
    ];

    public function classes()
    {
        return $this->belongsTo('App\Models\Classes');
    }

    public function income()
    {
        return $this->hasOne('App\Models\Income');
    }

    public function student_bill()
    {
        return $this->hasOne('App\Models\StudentBill');
    }
    
    public function student_class_histories()
    {
        return $this->hasMany('App\Models\StudentClassHistory');
    }
}
