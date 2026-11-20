<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionAllocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'account_id',
        'transaction_code_id',
        'date',
        'time',
        'desc',
        'amount',
        'user_id'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction_code()
    {
        return $this->belongsTo(TransactionCode::class);
    }

    public function transaction_code_sub()
    {
        return $this->belongsTo(TransactionCodeSub::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
