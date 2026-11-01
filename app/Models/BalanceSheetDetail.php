<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceSheetDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance_sheet_account_id',
        'year',
        'amount',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'amount' => 'decimal:2',
    ];

    public function balance_sheet_account()
    {
        return $this->belongsTo(BalanceSheetAccount::class);
    }
}
