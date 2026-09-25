<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupApplication extends Model
{
    
    use HasFactory;
    protected $connection = 'auth_mysql';
    protected $fillable =[
        'group_id',
        'application_id'
    ];
    
    public function group(){
        return $this->belongsTo('App\Models\Group');
    }

    public function application(){
        return $this->belongsTo('App\Models\Application');
    }

}
