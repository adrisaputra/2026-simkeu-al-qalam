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
}
