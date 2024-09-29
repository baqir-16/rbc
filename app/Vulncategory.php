<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vulncategory extends Model
{
    protected $table = 'vul_categories';
    protected $fillable = ['name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
