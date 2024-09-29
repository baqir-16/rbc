<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id','stream_id','comments','status'
    ];

  public function user()
  {
      return $this->belongsTo('App\User');
  }

  public function ticket()
  {
      return $this->hasOne('App\Ticket', 'id', 'pmo_comments_id');
//      return $this->hasOne('App\Ticket');
  }
}
