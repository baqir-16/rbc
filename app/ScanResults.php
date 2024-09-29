<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Moloquent\Eloquent\Model as Eloquent;

class ScanResults extends Eloquent {

    protected $collection = 'scan_results';

}
