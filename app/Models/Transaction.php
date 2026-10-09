<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'type',
        'transaction_code_id',
        'desc_category_id',
        'desc',
        'amount',
        'user_id'
    ];

    public function transaction_code()
    {
        return $this->belongsTo('App\Models\TransactionCode');
    }

    public function desc_category()
    {
        return $this->belongsTo('App\Models\DescCategory');
    }

    public function transaction_banks()
    {
        return $this->hasMany(TransactionBank::class);
    }
    
}
