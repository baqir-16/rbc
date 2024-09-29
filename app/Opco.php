<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Opco extends Model
{
    protected $table = 'opco';
    protected $fillable = ['name'];

  public function user()
  {
      return $this->belongsTo('App\User');
  }

  public function ticket()
  {
      return $this->hasMany('App\Ticket', 'id', 'ticket_id');
  }
}
