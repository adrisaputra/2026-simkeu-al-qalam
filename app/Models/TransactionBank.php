<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionBank extends Model
{
    use HasFactory;
    protected $fillable = [
        'desc_category_id',
        'student_id',
        'transaction_number',
        'date',
        'time',
        'amount',
        'user_id'
    ];

    public function desc_category()
    {
        return $this->belongsTo('App\Models\DescCategory');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
}
