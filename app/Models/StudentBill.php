<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBill extends Model
{
    use HasFactory;
     protected $fillable = [
        'student_id',
        'student_bill_type_id',
        'academic_year_id',
        'period_start',
        'period_end',
        'amount',
        'due_date',
        'notes',
    ];

    public function student(){
        return $this->belongsTo('App\Models\Student');
    }
    
    public function student_bill_type(){
        return $this->belongsTo('App\Models\StudentBillType');
    }
    
    public function academic_year(){
        return $this->belongsTo('App\Models\AcademicYear');
    }
    
}
