<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{

    protected $fillable = [
        'ticket_id', 'module_id','pmo_id', 'pmo_comments_id', 'pmo_assigned_date','tester_id','tester_comments_id','tester_assigned_date',
        'analyst_id','analyst_comments_id','analyst_assigned_date', 'qa_id','qa_comments_id','qa_assigned_date', 'hod_id','hod_comments_id',
        'hod_assigned_date','hod_signedoff_date','status'
    ];

    public function user()
    {
      return $this->hasMany('App\User', 'id', 'user_id');
    }

//    public function comments()
//    {
//        return $this->hasManyThrough(
//          'App\Comment',
//          'App\Ticket',
//          'pmo_comments_id',
//          'id'
//        );
//    }

    public function comments()
    {
        return $this->hasMany('App\Comment', 'id', 'pmo_comments_id');
    }

    public function opco()
    {
      return $this->hasMany('App\Opco','id', 'opco_id');
    }

    public function modules()
    {
      return $this->hasMany('App\Module', 'id', 'module_id');
    }

    public function tickets()
    {
      return $this->hasMany('App\Ticket', 'id', 'ticket_id');
    }
}
