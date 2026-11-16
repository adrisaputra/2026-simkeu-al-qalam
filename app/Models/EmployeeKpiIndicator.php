<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeKpiIndicator extends Model
{
    use HasFactory;
    protected $connection = 'kpi_mysql';
    protected $fillable = [
        'employee_kpi_period_id',
        'kpi_indicator_id',
        'indicator',
        'target',
        'weight',
        'score',
        'value'
    ];

    public function employee_kpi_period(){
        return $this->belongsTo('App\Models\EmployeeKpiPeriod');
    }

    public function kpi_indicator(){
        return $this->belongsTo('App\Models\KpiIndicator')->withTrashed();
    }

    public function employee_kpi_indicator_item(){
        return $this->hasOne('App\Models\EmployeeKpiIndicatorItem');
    }

}
