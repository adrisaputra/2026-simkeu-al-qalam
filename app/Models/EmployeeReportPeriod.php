<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReportPeriod extends Model
{
    use HasFactory;
    protected $connection = 'kpi_mysql';
    protected $fillable = [
        'employee_report_category_id',
        'employee_id',
        'day',
        'date',
        'is_locked',
    ];

    public function employee_report_category(){
        return $this->belongsTo('App\Models\EmployeeReportCategory');
    }
    
    public function employee(){
        return $this->belongsTo('App\Models\Employee');
    }

    public function employee_report(){
        return $this->hasOne('App\Models\EmployeeReport');
    }

    public function employee_reports(){
        return $this->hasMany('App\Models\EmployeeReport');
    }

}
