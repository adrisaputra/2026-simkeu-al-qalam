<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionBank extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'account_id',
        'transaction_code_id',
        'transaction_code_sub_id',
        'student_id',
        'student_name',
        'classes',
        'transaction_number',
        'date',
        'time',
        'import_date',
        'amount',
        'user_id',
        'status'
    ];

    public function desc_category()
    {
        return $this->belongsTo('App\Models\DescCategory');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
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
