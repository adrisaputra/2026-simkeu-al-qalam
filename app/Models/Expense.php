<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
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
    
}
