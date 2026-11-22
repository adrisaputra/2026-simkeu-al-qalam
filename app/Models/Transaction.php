<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'date',
        'type',
        'account_id',
        'transaction_code_id',
        'transaction_code_sub_id',
        'payment_account_id',
        'desc',
        'amount',
        'user_id',
    ];

    public function transaction_code()
    {
        return $this->belongsTo('App\Models\TransactionCode');
    }

    public function desc_category()
    {
        return $this->belongsTo('App\Models\DescCategory');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function transaction_bank()
    {
        return $this->hasOne(TransactionBank::class);
    }

    public function transaction_banks()
    {
        return $this->hasMany(TransactionBank::class);
    }
}
