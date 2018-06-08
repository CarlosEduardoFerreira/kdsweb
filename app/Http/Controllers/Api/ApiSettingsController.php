<?

namespace App\Http\Controllers\Api;


use PDO;

class ApiSettingsController {

    public static function getSettings(ApiConnectionController $db, array $request, array $response) {

        $settingsSQL = "SELECT * FROM settings WHERE store_guid_ = '" . $request["store_guid_"] . "'";
        $settingsRes = $db->query($settingsSQL);
        
        if ($settingsRow = $settingsRes->fetch(PDO::FETCH_ASSOC)) {
            
            $response[0]["server_address_"]            = $settingsRow["server_address_"];
            $response[0]["server_username_"]           = $settingsRow["server_username_"];
            $response[0]["server_password_"]           = $settingsRow["server_password_"];
            $response[0]["socket_port_"]               = $settingsRow["socket_port_"];
            $response[0]["auto_done_order_hourly_"]    = $settingsRow["auto_done_order_hourly_"];
            $response[0]["auto_done_order_time_"]      = $settingsRow["auto_done_order_time_"];
            $response[0]["timezone_"]                  = $settingsRow["timezone_"];
            $response[0]["smart_order_"]               = $settingsRow["smart_order_"];
            
            if(isset($settingsRow["licenses_quantity_"])) {
                $response[0]["licenses_quantity_"] = $settingsRow["licenses_quantity_"];
                
            } else {
                $response[0]["licensesQuantity"] = 0;
            }
        } else {

            $response[0]["error"]  = "It is not possible get settings for thi store.";
        }

        return $response;

    }

}

?>
