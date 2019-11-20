<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class TicketUser extends Model {
    
    protected $table = 'kdsticket_users';
    protected $fillable = [
        'name',
        'business_name',
        'email',
        'zipcode',
        'phone_number',
        'device_os',
        'device_model',
        'app_version',
        'create_time'
    ];
    
    public $timestamps = false;
}

?>