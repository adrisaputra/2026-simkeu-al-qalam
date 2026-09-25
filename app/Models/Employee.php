<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $connection = 'simpeg_mysql';
    protected $fillable = [
        'work_unit_id',
        'name',
        'nik',
        'niy',
        'niy_deed',
        'niy_month',
        'niy_year',
        'niy_serial_number',
        'birthplace',
        'birthdate',
        'gender',
        'address',
        'religion',
        'blood_type',
        'marital_status',
        'phone',
        'ethnic',
        'email',
        'education',
        'ig',
        'fb',
        'tiktok',
        'tmt',
        'file_ktp',
        'file_kk',
        'photo',
        'leaving_reason'
    ];
    
    public function user(){
        return $this->hasOne('App\Models\User');
    }

    public function employee_kpi(){
        return $this->hasOne('App\Models\EmployeeKpi');
    }

    public function employee_report(){
        return $this->hasOne('App\Models\EmployeeReport');
    }

    public function employee_report_period(){
        return $this->hasOne('App\Models\EmployeeReportPeriod');
    }

    public function work_unit(){
        return $this->belongsTo('App\Models\WorkUnit');
    }

}
