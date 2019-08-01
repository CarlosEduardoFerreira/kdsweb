<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class LicenseLog extends Model {
    
    protected $table = 'licenses_log';
    protected $fillable = ['store_guid', 'quantity', 'update_time', 'update_user'];
    
    public $timestamps = false;
    
    
}

?>