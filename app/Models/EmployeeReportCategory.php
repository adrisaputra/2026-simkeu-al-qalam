<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeReportCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'kpi_mysql';
    protected $fillable = [
        'employee_id',
        'report_category_id',
        'month',
        'year'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employe');
    }

    public function report_category(){
        return $this->belongsTo('App\Models\ReportCategory')->withTrashed();
    }
}
