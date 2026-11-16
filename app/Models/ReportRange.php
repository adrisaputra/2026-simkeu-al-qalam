<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportRange extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $connection = 'kpi_mysql';
    protected $fillable = [
        'report_category_id',
        'name',
        'min_value',
        'max_value',
        'score'
    ];

    public function report(){
        return $this->hasOne('App\Models\Report');
    }

    public function reports(){
        return $this->hasMany('App\Models\Report');
    }

}
