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
                
            } else if ($req == 'REGISTER_VALIDATION') {
                $response = $this->registerValidation($request);
                
            } else if ($req == 'SMS_ORDER') {
                $response = $this->smsOrder($request, $response);
            }
            
            return response()->json($response);
        }
        
    }
    
    
    public function login(array $request, array $response) {
        
        $username = $request["username"];
        $password = $request["password"];
        $device_serial = isset($request["serial"]) ? $request["serial"] : "";
        
        $sql = "SELECT
                    password,
                    store_guid,
                    business_name
                FROM users
                WHERE username = '$username'";
        
        $result = DB::select($sql);
        
        //echo "sql: " . $sql . "|";
        
        if (count($result) > 0) {
            
            $passDataBase = $result[0]->password;
            
            $passMatched = Hash::check($password, $passDataBase);
            
            if ($passMatched) {
                
                // Check if the same serial number is activate in another store.
                $sameSerialActive = DB::table('devices')
                    ->where('store_guid', '<>',  $result[0]->store_guid)
                    ->where('serial', '=', $device_serial)
                    ->where('is_deleted', '=', 0)
                    ->where('license', '=', 1)
                    ->where('split_screen_parent_device_id', '=', 0)
                    ->first();
                    
                if (isset($sameSerialActive)) {
                    $response[0]["error"]  = "There is another KDS Station with the same serial number active in another store.";
                    
                } else {
                    $response[0]["store_guid"] = $result[0]->store_guid;
                    $response[0]["store_name"] = $result[0]->business_name;
                    
                    $sqlStoreKey = "SELECT store_key FROM settings WHERE store_guid = '" .$result[0]->store_guid. "'";
                    $response[0]["store_key"] = DB::select($sqlStoreKey)[0]->store_key;
                }
                
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
    
    
    public function smsOrder(array $request, array $response) {
        
        $response[0]["start"] = "started";
        
        $orderStatus = $request["order_status"];
        
        $countOrderSMS = DB::table('sms_order_sent')
        ->where('store_guid', $request["store_guid"])
        ->where('order_guid', $request["order_guid"])
        ->where('order_status', $orderStatus)->count();
        
        // It must send SMS when it was not sent before for this order and order_status.
        if ($countOrderSMS == 0) {
            $response[0]["orderSMS"] = "orderSMS";
            $storeSettings = DB::table('settings')->where(['store_guid' => $request["store_guid"]])->first();
            
            $msg = "";
            
            $response[0]["storeSettings"] = "storeSettings";
            if (isset($storeSettings)) {
                
                $adminSettings = DB::table('admin_settings')->first();
                
                $sms_start_use_default = $storeSettings->sms_start_use_default !== null ? $storeSettings->sms_start_use_default : 0;
                $sms_ready_use_default = $storeSettings->sms_ready_use_default !== null ? $storeSettings->sms_ready_use_default : 0;
                $sms_done_use_default = $storeSettings->sms_done_use_default !== null ? $storeSettings->sms_done_use_default : 0;
                
                if ( ($orderStatus == 'KDS_IOS.Item.BumpStatus.new' || $orderStatus == 'new' || $orderStatus == '0') && 
                    isset($storeSettings->sms_start_enable) && $storeSettings->sms_start_enable) {
                            
                    $response[0]["new"] = $request["order_status"];
                    if ($sms_start_use_default) {
                        $msg = $adminSettings->sms_order_start_message;
                    } else {
                        $msg = $storeSettings->sms_start_custom;
                    }
                    
               } else if ( ($orderStatus == 'KDS_IOS.Item.BumpStatus.prepared' || $orderStatus == 'prepared' || $orderStatus == '1') && 
                    isset($storeSettings->sms_ready_enable) && $storeSettings->sms_ready_enable) {
                            
                    $response[0]["ready"] = $request["order_status"];
                    if ($sms_ready_use_default) {
                        $msg = $adminSettings->sms_order_ready_message;
                    } else {
                        $msg = $storeSettings->sms_ready_custom;
                    }
                    
               } else if ( ($orderStatus == 'KDS_IOS.Item.BumpStatus.done' || $orderStatus == 'done' || $orderStatus == '2') && 
                    isset($storeSettings->sms_done_enable) && $storeSettings->sms_done_enable) {
                            
                    $response[0]["done"] = $request["order_status"];
                    if ($sms_done_use_default) {
                        $msg = $adminSettings->sms_order_done_message;
                    } else {
                        $msg = $storeSettings->sms_done_custom;
                    }
                }

                $response[0]["msg"] = $msg;

                if (isset($msg) && !is_null($msg) && $msg != "") {
                    if (isset($request["store_name"])) {
                        $msg = str_replace("[STORE_NAME]", $request["store_name"], $msg);
                    }

                    $response[0]["msg"] = $msg;

                    $validAccount = trim($storeSettings->sms_account_sid) != "";
                    $validAccount = $validAccount && trim($storeSettings->sms_token) != "";
                    $validAccount = $validAccount && trim($storeSettings->sms_phone_from) != "";
                    
                    if (!$validAccount) {
                        $response[0]["error"] = "Invalid account settings.";
                        
                    } else if (isset($request["order_phone"]) && !is_null($request["order_phone"]) && $validAccount) {
                        
                        $response[0]["phone"] = $request["order_phone"];
                        
                        try {
                            $create_time = (new DateTime())->getTimestamp();
                            $sql = "INSERT INTO sms_order_sent (store_guid, order_guid, order_status, sms_message, create_time)
                                VALUES('".$request["store_guid"]."', '".$request["order_guid"]."', '".$request["order_status"]."', '".$msg."', $create_time)";
                            $insert_result = DB::statement($sql);
                            
                            $response[0]["sms_order_sent_insert_result"] = $insert_result;
                        
                        } catch (\Exception $e) {
                            $response[0]["error"] = "SMS already registered by other process.";
                            $insert_result = false;
                        }
                        // Check again to prevent send the same SMS twice or more times.
                        // store_guid, order_guid and order_status are primary key on DB.
                        // If it already exist on DB it will not insert and also not proceed.
                        if ($insert_result) {
                            require_once("Twilio.php");
                            $sms = new ManagerSMS();
                            $sms->configTwilio($storeSettings->sms_account_sid, $storeSettings->sms_token, $storeSettings->sms_phone_from);
                            $sms_result = $sms->sendSMS($request["order_phone"], $msg);
                            
                            $response[0]["sms_result"] = $sms_result;
                        }

                        
                    } else {
                        $response[0]["error"] = "Invalid phone number.";
                    }

                }
            }
            
            
        } else {
            $response[0]["error"] = "SMS already registered.";
        }
        
        return $response;
        
    }
    
    
    public function getSettings(array $request, array $response) {
        $sql = DB::table('settings')->where(['store_guid' => $request["store_guid"]]);

        if (isset($request["min_update_time"])) {
            $sql->where("update_time", ">", $request["min_update_time"]);
        }

        $settingsRes = $sql->first();
        
        if (isset($settingsRes)) {

            $settingsArray = json_decode(json_encode($settingsRes), true);

            $response = array($settingsArray);

            // Admin Global Settings
            $adminSettings = DB::table('admin_settings')->first();

            $response[0]["offline_limit_hours"]      = $adminSettings->offline_limit_hours;
            $response[0]["sms_order_start_message"]  = $adminSettings->sms_order_start_message;
            $response[0]["sms_order_ready_message"]  = $adminSettings->sms_order_ready_message;
            $response[0]["sms_order_done_message"]   = $adminSettings->sms_order_done_message;

            if(isset($settingsRes->licenses_quantity)) {
                $response[0]["licenses_quantity"] = $settingsRes->licenses_quantity;

            } else {
                $response[0]["licenses_quantity"] = 0;
            }

        } else if (!isset($request["min_update_time"])) {
            $response[0]["error"]  = "It is not possible get settings for this store.";
        }
        
        return $response;
        
    }
    
    
    public function getDevices(array $request, array $response) {
        
        $sql = "SELECT * FROM devices WHERE store_guid = '" . $request["store_guid"] . "'";

        if (isset($request["min_update_time"])) {
            $sql .= " AND update_time > " . $request["min_update_time"];

        } else {
            $sql .= " AND is_deleted != 1";

        }

        //echo "sql: " . $sql . "|";

        return DB::select($sql);
        
    }
    
    
    public function registerValidation(Request $request) {
        $return = array();
        $user = DB::table('users')->where('id', '<>', $request->id)->where('email', '=', $request->email)->first();
        if (isset($user)) {
            if (isset($user->email)) {
                $return["FIELD"] = "email";
                $return["ERROR"] = "This email is already in use.";
            }
        }
        
        if (count($return) == 0) {
            $user = DB::table('users')->where('id', '<>', $request->id)->where('username', '=', $request->username)->first();
            if (isset($user)) {
                if (isset($user->username)) {
                    $return["FIELD"] = "username";
                    $return["ERROR"] = "This username is already in use.";
                }
            }
        }
        
        return array($return);
    }
    
    
    public function activeLicense(Request $request) {
        if ($request->active) {
            $device = DB::table('devices')->where(['guid' => $request->guid])->first();
            if (isset($device)) {
                if (isset($device->serial)) {
                    $sameSerialActive = DB::table('devices')
                    //->where('store_guid', '=',  $request->store_guid) // should permit same serial for the same store?
                    ->where('guid', '<>',  $request->guid)
                    ->where('serial', '=', $device->serial)
                    ->where('is_deleted', '=', 0)
                    ->where('license', '=', 1) // should permit same serial active license?
                    ->where('split_screen_parent_device_id', '=', 0)
                    ->first();
                    if (isset($sameSerialActive)) {
                        return array("There is another KDS Station with the same serial number active.");
                    }
                }
            }
        }
        
        $update_time = (new DateTime())->getTimestamp();
        $sql = "update devices set license = $request->active where guid = '$request->guid'";
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





