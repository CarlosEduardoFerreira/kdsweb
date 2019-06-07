<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Plan extends Model {
    
    protected $table = 'plans';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    
    protected $fillable = ['guid', 'base_plan', 'name', 'cost', 'payment_type', 'app', 'status', 'default', 
                            'create_time', 'update_time', 'update_user', 'owner_id'];
    
    public $timestamps = false;
    
}

?>