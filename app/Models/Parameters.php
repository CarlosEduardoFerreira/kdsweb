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
            $result = Parameters::where('param', $param)->first();
            if (!isset($result)) {
                return $default;
            } 
            return $result->value;
        } catch (Exception $e) {
            return $default;
        }
    }
}

?>