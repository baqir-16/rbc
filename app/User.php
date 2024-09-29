<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','username', 'email', 'password','opco_id', 'avatar', 'department'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function department()
    {
        return $this->hasMany('App\Department', 'id');
    }

    public function opco()
    {
        return $this->hasOne('App\Opco', 'opco','id', 'opco_id');
    }

    public function streams()
    {
        return $this->hasMany('App\Stream');
    }

    public function pdfreports ()
    {
        return $this->hasMany('App\Pdfreport');
    }
    public function vulncategories ()
    {
        return $this->hasMany('App\Vulncategory');
    }
}
