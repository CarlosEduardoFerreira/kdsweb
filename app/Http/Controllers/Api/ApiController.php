<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// Even eclipse shows the errors below we need keep this, at least laravel will not work.
// use App\Http\Controllers\Api;
// use App\Http\Controllers\Api\ApiSyncController;
// use App\Http\Controllers\Api\ApiUserController;
// use App\Http\Controllers\Api\ApiSettingsController;
// use App\Http\Controllers\Api\ApiDeviceController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $request = file_get_contents("php://input");
        $request = $this->stripslashes_deep(htmlspecialchars_decode($request));
        $request = json_decode($request, true);

        $response = array(array());

        /** // Request
         *  req = Resquest/Function
         */
        $req = $request["req"];

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            
//             $db = new ApiConnectionController();
//             $db->create();
            
            if($req == "SYNC") {

//                 $response = ApiSyncController::InsertOrUpdateEntityWeb($request, $response);
                
                $response = $this->insertOrUpdateEntityWeb($request, $response);

            } else if($req == "LOGIN") {

//                 $response = ApiUserController::login($request, $response);

                $response = $this->login($request, $response);

            } else if($req == "GET_SETTINGS") {
                
//                 $response = ApiSettingsController::getSettings($request, $response);

                $response = $this->getSettings($request, $response);
                
            } else if($req == "GET_DEVICES") {

                $response = $this->getDevices($request, $response);
                
            }
            
//             $db->close();
            
            return response()->json($response);
        }

    }
    
    
    public function insertOrUpdateEntityWeb(array $request, array $response) {
        
        $entity = $request["entity"];
        $data   = $request["data"];
        // print_r($data);
        foreach ($data as $object) {
            // echo "api5 |";
            // addslashes($object);
            // print_r($object);
            
            $func = "UPD"; // Update
            
            $sqlCheck   = "SELECT 1 FROM $entity WHERE guid_ = " . $object['guid_'];
            //echo "sqlCheck: $sqlCheck";
            $result     = DB::select($sqlCheck);
            if (count($result) == 0) {
                $func = "INS"; // Insert
            }
            
            $guid = "";
            $updateTime = 0;
            
            if($func == "INS") {
                $sql  = "INSERT INTO $entity ";
                $sql .= "(";
                foreach($object as $key=>$value) {
                    $sql .= "$key , ";
                }
                $sql  = rtrim($sql , ", ");
                $sql .= ") VALUES(";
                
            } else {
                $sql = "UPDATE $entity SET ";
            }
            
            foreach($object as $key=>$value) {
                if(!is_array($value)) {
                    if($func == "INS") {
                        $sql .= "$value , ";
                    } else {
                        $sql .= "$key = $value , ";
                    }
                }
            }
            
            $sql  = rtrim($sql , ", ");
            
            if($func == "INS") {
                $sql .= ")";
            } else {
                $sql .= " WHERE guid_ = " . $object['guid_'] . " AND update_time_ < " . $object['update_time_'];
            }
            
            //             echo "sql: $sql";
            $result = DB::statement($sql);
            
            if($result) {
                $response["result"]  = "OK";
            } else {
                $response["error"]  = "Error trying $func: $result";
            }
            
        }
        
        return $response;
        
    }
    
    
    public function login(array $request, array $response) {
        
        $username = $request["username"];
        $password = $request["password"];
        
        $sql = "SELECT
                    password,
                    store_guid
                FROM users
                WHERE username = '$username'";
        
        $result = DB::select($sql);
        
        //echo "sql: " . $sql . "|";
        
        if (count($result) > 0) {
            
            $passDataBase = $result[0]->password;
            
            $passMatched = Hash::check($password, $passDataBase);
            
            if ($passMatched) {
                
                $response[0]["store_guid_"] = $result[0]->store_guid;
                
                $request["store_guid_"]     = $result[0]->store_guid;
                
                // include store settings on response
                $response = $this->getSettings($request, $response);
                
                if($response[0]["licenses_quantity_"] == 0) {
                    $response[0]["error"]  = "There is no license available.";
                    
                } 
                /* For now we will not use this part because even the store does not have license available
                 * the tablet that the user is trying setup can be a swap and not new one.
                else {
                    $devices = $this->getDevices($request, $response);
                    
                    $licensesInUse = 0;
                    foreach ($devices as $device) {
                        $licensesInUse += $device["login_"] == 1 ? 1 : 0;
                    }
                    
                    if ($response[0]["licenses_quantity_"] <= $licensesInUse) {
                        $response[0]["error"]  = "There is no license available.";
                    }
                }
                */
                
            } else {
                
                $response[0]["error"]  = "Password is incorrect.";
            }
            
        } else {
            
            $response[0]["error"]  = "Username is incorrect.";
        }
        
        return $response;
        
    }
    
    
    public function getSettings(array $request, array $response) {
        
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
    
    
    public function getDevices(array $request, array $response) {
        
        $sql = "SELECT * FROM devices WHERE store_guid_ = '" . $request["store_guid_"] . "'";
        
        //echo "sql: " . $sql . "|";
        
        return DB::select($sql);
        
    }
    

    public function stripslashes_deep($value) {
        $value = is_array($value) ?
        array_map('stripslashes_deep', $value) :
        stripslashes($value);

        return $value;
    }

}
