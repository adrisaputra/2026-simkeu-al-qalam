<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $connection = 'auth_mysql';
    protected $fillable =[
        'name'
    ];
    
    public function user(){
        return $this->hasOne('App\Models\User');
    }

    public function group_application(){
        return $this->hasOne('App\Models\GroupApplication');
    }

}
