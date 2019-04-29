<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class App extends Model {
    
    protected $table = 'apps';
    protected $fillable = ['guid', 'name', 'enable', 'image', 'create_time', 'update_time'];
    
    public $timestamps = false;
    
    
}

?>