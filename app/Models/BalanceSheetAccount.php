<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceSheetAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'transaction_code_id',
        'transaction_code_sub_id',
        'name',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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

    public function balance_sheet_details()
    {
        return $this->hasMany(BalanceSheetDetail::class);
    }
}
