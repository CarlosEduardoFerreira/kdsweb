<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Parameters extends Model {
    
    protected $table = 'parameters';
    protected $fillable = [
        'param',
        'value'
    ];
    
    public $timestamps = false;

    public static function getValue($param, $default = null) {
        try {
            return Parameters::where('param', $param)->first()->value;
        } catch (Exception $e) {
            return $default;
        }
    }
}

?>