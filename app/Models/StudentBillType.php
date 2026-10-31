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
        'transaction_code_sub_id',
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
    
    public function transaction_code_sub(){
        return $this->belongsTo('App\Models\TransactionCodeSub');
    }
    
    public function student_bill()
    {
        return $this->hasOne('App\Models\StudentBill');
    }
}
