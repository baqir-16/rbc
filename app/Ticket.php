<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
      'title','ref','user_id', 'department', 'pmo_comments_id', 'opco_id', 'status', 'updated_at'
    ];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment', 'id', 'pmo_comments_id');
    }

    public function department()
    {
        return $this->hasMany('App\Department', 'id');
    }

    public function opco()
    {
        return $this->hasMany('App\Opco','id', 'opco_id');
    }

    public function streams()
    {
        return $this->hasMany('App\Stream', 'id', 'stream_id');
    }

    public function modules()
    {
        return $this->hasMany('App\Module', 'id', 'module_id');
    }
}
