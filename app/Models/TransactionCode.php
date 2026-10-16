<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'code',
        'name',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function expense()
    {
        return $this->hasOne('App\Models\Expense');
    }

    public function transaction_code_subs()
    {
        return $this->hasMany('App\Models\TransactionCodeSub');
    }
}
