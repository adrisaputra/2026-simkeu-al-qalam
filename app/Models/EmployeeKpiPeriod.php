<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeKpiPeriod extends Model
{
    use HasFactory;
    protected $connection = 'kpi_mysql';
    protected $fillable = [
        'employee_kpi_id',
        'employee_id',
        'month',
        'year'
    ];

    public function employee_kpi(){
        return $this->belongsTo('App\Models\EmployeeKpi');
    }

    public function employee(){
        return $this->belongsTo('App\Models\Employee');
    }

    public function employee_kpi_indicator(){
        return $this->HasOne('App\Models\EmployeeKpiIndicator');
    }

    public function employee_kpi_bonus(){
        return $this->HasOne('App\Models\EmployeeKpiBonus');
    }

}
