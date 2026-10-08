<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'classes_id',
        'nis',
        'name'
    ];

    public function classes()
    {
        return $this->belongsTo('App\Models\Classes');
    }
    
    public function income()
    {
        return $this->hasOne('App\Models\Income');
    }

}
