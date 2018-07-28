<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// Even eclipse shows the errors below we need keep this, at least laravel will not work.
// use App\Http\Controllers\Api;
// use App\Http\Controllers\Api\ApiSyncController;
// use App\Http\Controllers\Api\ApiUserController;
// use App\Http\Controllers\Api\ApiSettingsController;
// use App\Http\Controllers\Api\ApiDeviceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use DateTime;
use DateTimeZone;



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
        
        // TOKEN - THIS CANNOT BE CHANGED!!! -------------------------------------------------------------------------- //
        if (!isset($request[0]["tok"]) || $request[0]["tok"] != "c0a6r1l1o9sL6t2h4gjhak7hf3uf9h2jnkjdq37qh2jk3fbr1706") {
            $response[0]["error"]  = "Your application has no permission to do this!";
            return response()->json($response);
        } else {
            $request = $request[1];
        }
        // -------------------------------------------------------------------------- TOKEN - THIS CANNOT BE CHANGED!!! //
        
        /** // Request
         *  req = Resquest/Function
         */
        $req = $request["req"];
        
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            
            if ($req == "LOGIN") {
                $response = $this->login($request, $response);
                
            } else if ($req == "SYNC") {
                $response = $this->insertOrUpdateEntityWeb($request, $response);
                
            } else if ($req == "GET_SETTINGS") {
                $response = $this->getSettings($request, $response);
                
            } else if ($req == "GET_DEVICES") {
                $response = $this->getDevices($request, $response);
                
            } else if ($req == "DEVICES_ACTIVE") {
                $response = $this->activeLicense($request, $response);
                
            } else if ($req == "DEVICE_ONLINE") {
                $response = $this->setDeviceOnline($request, $response);
                
            } else if ($req == 'SYNC_ORDER') {
                $response = $this->syncOrder($request, $response);
            }
            
            return response()->json($response);
        }
        
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
                
                $response[0]["store_guid"] = $result[0]->store_guid;
                
                //$request["store_guid_"]     = $result[0]->store_guid;
                
                // include store settings on response
                //$response = $this->getSettings($request, $response);
                
                //                 if($response[0]["licenses_quantity_"] == 0) {
                //                     $response[0]["error"]  = "There is no license available.";
                
                //                 }
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
    
    
    public function insertOrUpdateEntityWeb(array $request, array $response) {
        
        $entity = $request["entity"];
        $data   = $request["data"];
        
        $objGuidArray = array();
        
        foreach ($data as $object) {
            
            $func = "UPD"; // Update
            
            $guid = $object['guid'];
            $updt = $object['update_time'];
            
            $sqlCheck   = "SELECT 1 FROM $entity WHERE guid = $guid";
            //echo "sqlCheck: $sqlCheck";
            $result     = DB::select($sqlCheck);
            if (count($result) == 0) {
                $func = "INS"; // Insert
            }
            
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
                        if ($key == "guid") {
                            continue;
                        }
                        $sql .= "$key = $value , ";
                    }
                }
            }
            
            $sql  = rtrim($sql , ", ");
            
            if($func == "INS") {
                $sql .= ")";
            } else {
                $sql .= " WHERE guid = $guid AND (update_time < $updt OR update_time IS NULL)";
            }
            
            $result = DB::statement($sql);
            
            if ($result) {
                array_push($objGuidArray, $guid);
                
            } else {
                $response[0]["error"]  = "Error trying $func: $sql";
                break;
            }
            
        }
        
        $response = DB::select("SELECT * FROM $entity WHERE guid IN (" . implode(",", $objGuidArray) .")");
        
        return $response;
        
    }
    
    
    public function syncOrder(array $request, array $response) {
        
        $response[0]["start"] = "started";
        
        $orderSMS = DB::table('sms_order_sent')
        ->where('store_guid', $request["store_guid"])
        ->where('order_guid', $request["order_guid"])
        ->where('order_status', $request["order_status"])->first();
        
        // It must send SMS when it was not sent before for this order and order_status.
        if (!isset($orderSMS)) {
            $response[0]["orderSMS"] = "orderSMS";
            $storeSettings = DB::table('settings')->where(['store_guid' => $request["store_guid"]])->first();
            
            $msg = "";
            
            $response[0]["storeSettings"] = "storeSettings";
            if (isset($storeSettings)) {
                $adminSettings = DB::table('admin_settings')->first();
                
                if ($request["order_status"] == 'new' && isset($storeSettings->sms_start_enable) && $storeSettings->sms_start_enable) {
                    $response[0]["new"] = $request["order_status"];
                    if ($storeSettings->sms_start_use_default) {
                        $msg = $adminSettings->sms_order_start_message;
                    } else {
                        $msg = $storeSettings->sms_start_custom;
                    }
                    
                } else if ($request["order_status"] == 'ready' && isset($storeSettings->sms_ready_enable) && $storeSettings->sms_ready_enable) {
                    $response[0]["ready"] = $request["order_status"];
                    if ($storeSettings->sms_ready_use_default) {
                        $msg = $adminSettings->sms_order_ready_message;
                    } else {
                        $msg = $storeSettings->sms_ready_custom;
                    }
                    
                } else if ($request["order_status"] == 'done' && isset($storeSettings->sms_done_enable) && $storeSettings->sms_done_enable) {
                    $response[0]["done"] = $request["order_status"];
                    if ($storeSettings->sms_done_use_default) {
                        $msg = $adminSettings->sms_order_done_message;
                    } else {
                        $msg = $storeSettings->sms_done_custom;
                    }
                    
                }
                
                $response[0]["msg"] = $msg;
                if (isset($msg) && !is_null($msg) && $msg != "") {
                    $order = DB::table('orders')->where(['guid' => $request["order_guid"]])->first();
                    
                    $response[0]["phone"] = $order->phone;
                    if (isset($order->phone) && !is_null($order->phone)) {
                        require_once("Twilio.php");
                        $sms = new ManagerSMS();
                        $return = $sms->sendSMS($order->phone, $msg);
                        
                        $response[0]["sms_result"] = $return;
                    }
                    
                    if ($return) {
                        $sql = "INSERT INTO sms_order_sent (store_guid, order_guid, order_status, sms_message)
                            VALUES('".$request["store_guid"]."', '".$request["order_guid"]."', '".$request["order_status"]."', '".$msg."')";
                        $result = DB::statement($sql);
                        $response[0]["sms_order_sent_insert_result"] = $result;
                    }

                }
            }
            
            
        } else {
            $response[0]["error"] = "Nothing to do.";
        }
        
        return $response;
        
    }
    
    
    public function getSettings(array $request, array $response) {
        
        $settingsRes = DB::table('settings')->where(['store_guid' => $request["store_guid"]])->first();
        
        $adminSettings = DB::table('admin_settings')->first();
        
        if (isset($settingsRes)) {
            
            $response[0]["guid"]                      = $settingsRes->guid;
            $response[0]["server_address"]            = $settingsRes->server_address;
            $response[0]["server_username"]           = $settingsRes->server_username;
            $response[0]["server_password"]           = $settingsRes->server_password;
            $response[0]["socket_port"]               = $settingsRes->socket_port;
            $response[0]["auto_done_order_hourly"]    = $settingsRes->auto_done_order_hourly;
            $response[0]["auto_done_order_time"]      = $settingsRes->auto_done_order_time;
            $response[0]["timezone"]                  = $settingsRes->timezone;
            $response[0]["smart_order"]               = $settingsRes->smart_order;
            $response[0]["last_connection_time"]      = $settingsRes->last_connection_time;
            
            // Admin Global Settings
            $response[0]["offline_limit_hours"]       = $adminSettings->offline_limit_hours;
            
            if(isset($settingsRes->licenses_quantity)) {
                $response[0]["licenses_quantity"] = $settingsRes->licenses_quantity;
                
            } else {
                $response[0]["licenses_quantity"] = 0;
            }
            
        } else {
            
            $response[0]["error"]  = "It is not possible get settings for this store.";
        }
        
        return $response;
        
    }
    
    
    public function getDevices(array $request, array $response) {
        
        $sql = "SELECT * FROM devices WHERE store_guid = '" . $request["store_guid"] . "' AND is_deleted != 1";
        
        //echo "sql: " . $sql . "|";
        
        return DB::select($sql);
        
    }
    
    
    public function activeLicense(Request $request) {
        if ($request->active) {
            $device = DB::table('devices')->where(['guid' => $request->guid])->first();
            if (isset($device)) {
                if (isset($device->serial)) {
                    $sameSerialActive = DB::table('devices')
                    ->where('guid', '<>',  $request->guid)
                    ->where('serial', '=', $device->serial)
                    ->where('is_deleted', '=', 0)
                    ->where('license', '=', 1)
                    ->where('split_screen_parent_device_id', '=', 0)
                    ->first();
                    if (isset($sameSerialActive)) {
                        return array("There is another KDS Station with the same serial number active.");
                    }
                }
            }
        }
        
        $update_time = (new DateTime())->getTimestamp();
        $sql = "update devices set license = $request->active , update_time = $update_time where guid = '$request->guid'";
        $result = DB::statement($sql);
        
        return array($result);
    }
    
    
    public function setDeviceOnline(array $request, array $response) {
        $deviceUpdated = array();
        
        $storeGuid = $request["store_guid"];
        $lastConnectionTime = $request["last_connection_time"];
        
        $device = DB::table('settings')->where('store_guid', '=', $storeGuid)->first();
        if (isset($device)) {
            $sql = "update settings set last_connection_time = $lastConnectionTime where store_guid = '$storeGuid'";
            $result = DB::statement($sql);
            
            if ($result) {
                $deviceUpdated = DB::select("SELECT * FROM settings WHERE store_guid = '$storeGuid'");
            } else {
                $deviceUpdated[0]["error"]  = "Error trying update device. sql: $sql";
            }
        } else {
            $deviceUpdated[0]["error"]  = "Device not found.";
        }
        return $deviceUpdated;
    }
    
    
    public function stripslashes_deep($value) {
        $value = is_array($value) ?
        array_map('stripslashes_deep', $value) :
        stripslashes($value);
        
        return $value;
    }
    
}





