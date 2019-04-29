<?php

namespace App\Models\Settings;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Faker\Provider\Payment;


class PaymentType extends Model {
    
    protected $table = 'payment_types';
    protected $fillable = ['guid', 'name', 'status', 'create_time', 'update_time', 'update_user'];
    
    public $timestamps = false;
    
}

?>