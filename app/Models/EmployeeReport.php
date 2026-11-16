<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReport extends Model
{
    use HasFactory;
    protected $connection = 'kpi_mysql';
    protected $fillable = [
        'employee_report_period_id',
        'report_id',
        'category',
        'name',
        'value',
        'employee_id',
        'reason'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employee');
    }
    
    public function employee_report_period(){
        return $this->belongsTo('App\Models\EmployeeReportPeriod');
    }
    
    public function report(){
        return $this->belongsTo('App\Models\Report')->withTrashed();
    }

    public function employee_report_file(){
        return $this->hasOne('App\Models\EmployeeReportFile');
    }
    
    public function employee_report_files(){
        return $this->hasMany('App\Models\EmployeeReportFile');
    }
    
}
