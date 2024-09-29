<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pdfreport extends Model
{
    protected $fillable = ['name', 'img', 'disclaimer', 'm_title', 'm_description', 'c_hours',
        'c_responsible', 'c_escalation', 'h_hours', 'h_responsible', 'h_escalation', 'm_hours',
        'm_responsible', 'm_escalation', 'l_hours', 'l_responsible', 'l_escalation'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
