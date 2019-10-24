<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Order extends Model {
    
    protected $table = 'orders';
 
    protected $fillable = ['guid', 'store_guid', 'destination', 'external_id', 'guest_table', 'is_priority', 'items_count', 
    'order_type', 'pos_terminal', 'server_name', 'user_info', 'done', 'create_time', 'update_time', 'upload_time', 'is_deleted',
    'update_device', 'phone', 'create_local_time', 'is_hidden', 'customer_guid', 'smart_order_start_time', 'preparation_time'];
    
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;
}
 
?>