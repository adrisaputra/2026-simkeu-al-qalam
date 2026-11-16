<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'salary',
        'allowance1',
        'allowance2',
        'allowance3',
        'ttq',
        'salary_adjustment',
        'bos',
        'jp',
        'meal_allowance',
        'rumbay',
        'salary_amount',
        'deduction',
        'amount_received',
        'status',
    ];
    
    public function employee(){
        return $this->belongsTo('App\Models\Employee')->withTrashed();
    }

}
