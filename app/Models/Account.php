<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function transaction_code()
    {
        return $this->hasOne('App\Models\TransactionCode');
    }

    public function transaction_codes()
    {
        return $this->hasMany('App\Models\TransactionCode');
    }
}
