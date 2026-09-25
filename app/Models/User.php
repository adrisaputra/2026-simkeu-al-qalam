<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'auth_mysql';
    protected $fillable = [
        'name',
        'email',
        'password',
        'group_id',
        'employee_id',
        'work_unit_id',
        'photo',
        'phone',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
        
    public function group(){
        return $this->belongsTo('App\Models\Group');
    }

    public function employee(){
        return $this->belongsTo('App\Models\Employee');
    }

    public function work_unit(){
        return $this->belongsTo('App\Models\WorkUnit');
    }

    public function isAdminKPI()
    {
        return $this->group->name == 'Admin KPI';
    }

    public function isEmployee()
    {
        return $this->group->name == 'Employee';
    }

}
