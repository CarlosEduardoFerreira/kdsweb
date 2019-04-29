<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;


class PlanXObject extends Model {
    
    protected $table = 'plans_x_objects';
    protected $fillable = ['plan_guid', 'user_id'];
    
    public $timestamps = false;
}

?>