<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name', 'model', 'info', 'price', 'version', 'status'];

  public function user()
  {
      return $this->belongsTo('App\User');
  }
  public function tickets()
  {
      return $this->hasMany('App\Ticket');
  }
}
