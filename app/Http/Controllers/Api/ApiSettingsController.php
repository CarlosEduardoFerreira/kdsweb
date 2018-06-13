<?

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;


class ApiSettingsController {

    public static function getSettings(array $request, array $response) {

//         $settingsSQL = "SELECT * FROM settings WHERE store_guid_ = '" . $request["store_guid_"] . "'";
//         $settingsRes = DB::select($settingsSQL);
        
        $settingsRes = DB::table('settings')->where(['store_guid_' => $request["store_guid_"]])->first();
        
        if (isset($settingsRes)) {
            
            $response[0]["guid_"]                      = $settingsRes->guid_;
            $response[0]["server_address_"]            = $settingsRes->server_address_;
            $response[0]["server_username_"]           = $settingsRes->server_username_;
            $response[0]["server_password_"]           = $settingsRes->server_password_;
            $response[0]["socket_port_"]               = $settingsRes->socket_port_;
            $response[0]["auto_done_order_hourly_"]    = $settingsRes->auto_done_order_hourly_;
            $response[0]["auto_done_order_time_"]      = $settingsRes->auto_done_order_time_;
            $response[0]["timezone_"]                  = $settingsRes->timezone_;
            $response[0]["smart_order_"]               = $settingsRes->smart_order_;
            
            if(isset($settingsRes->licenses_quantity_)) {
                $response[0]["licenses_quantity_"] = $settingsRes->licenses_quantity_;
                
            } else {
                $response[0]["licenses_quantity_"] = 0;
            }
            
        } else {

            $response[0]["error"]  = "It is not possible get settings for this store.";
        }

        return $response;

    }

}

?>
