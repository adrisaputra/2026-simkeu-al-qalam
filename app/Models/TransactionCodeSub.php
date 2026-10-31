<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCodeSub extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code_id',
        'code',
        'name',
    ];

    public function transaction_code()
    {
        return $this->belongsTo(TransactionCode::class);
    }
    
    public function student_bill_type()
    {
        return $this->hasOne('App\Models\StudentBillType');
    }

}
