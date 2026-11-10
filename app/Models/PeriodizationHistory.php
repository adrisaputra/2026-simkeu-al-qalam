<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodizationHistory extends Model
{
    use HasFactory;
    protected $connection = 'simpeg_mysql';
    protected $fillable = [
        'employee_id',
        'periodization',
        'sk_number',
        'date',
        'date_periodization',
        'file',
        'status',
        'note',
        'desc'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employee');
    }
}
