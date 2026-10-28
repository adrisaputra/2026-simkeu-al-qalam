<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBillType extends Model
{
    use HasFactory;
    protected $fillable = [
        'work_unit_id',
        'transaction_code_id',
        'frequency',
        'default_amount',
        'is_active',
    ];

    public function work_unit(){
        return $this->belongsTo('App\Models\WorkUnit');
    }
    
    public function transaction_code(){
        return $this->belongsTo('App\Models\TransactionCode');
    }
    
}
