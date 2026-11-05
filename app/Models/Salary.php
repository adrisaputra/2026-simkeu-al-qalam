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
        'allowance4',
        'allowance5',
        'allowance6',
        'salary_adjustment',
        'bos',
        'meal_allowance',
        'rumbay',
        'salary_amount',
        'deduction',
        'amount_received',
        'status',
    ];
}
