<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeKpiBonus extends Model
{
    use HasFactory;
    protected $connection = 'kpi_mysql';
    protected $fillable = [
        'employee_kpi_period_id',
        'kpi_bonus_id',
        'category',
        'indicator',
        'target',
        'weight',
        'score',
        'value'
    ];

    public function employee_kpi_period(){
        return $this->belongsTo('App\Models\EmployeeKpiPeriod');
    }

    public function kpi_bonus(){
        return $this->belongsTo('App\Models\KpiBonus');
    }

    public function employee_kpi_indicator_item(){
        return $this->hasOne('App\Models\EmployeeKpiIndicatorItem');
    }

}
