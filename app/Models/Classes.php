<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = [
        'work_unit_id',
        'name'
    ];

    public function work_unit()
    {
        return $this->belongsTo('App\Models\WorkUnit');
    }

    public function student()
    {
        return $this->hasOne('App\Models\Student');
    }

    public function students()
    {
        return $this->hasMany('App\Models\Student');
    }

}
