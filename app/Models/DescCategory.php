<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'entered_by'
    ];

    public function income()
    {
        return $this->hasOne('App\Models\Income');
    }

    public function expense()
    {
        return $this->hasOne('App\Models\Expense');
    }
    
}
