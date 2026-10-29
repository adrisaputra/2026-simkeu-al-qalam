<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBillAcademicYear extends Model
{
     use HasFactory;
     protected $fillable = [
        'work_unit_id',
        'academic_year_id',
        'student_bill_type_id'
    ];
    
    public function work_unit(){
        return $this->belongsTo('App\Models\WorkUnit');
    }

    public function academic_year(){
        return $this->belongsTo('App\Models\AcademicYear');
    }
    
    public function student_bill_type(){
        return $this->belongsTo('App\Models\StudentBillType');
    }
    
}
