<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Settings\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    private $premium = array();
    
    private $request = "";
    private $response = array(array());
    private $method = "";
    private $requestError;
    
    private $error_exist_device_in_another_store = "There is another KDS Station with the same serial number active in another store.";
    
    
    public function __construct() {
        $this->DB = DB::class;
        $this->premium["sync_tables"] = [];
    }
    

    public function getRequest() {
        $this->request = file_get_contents("php://input");
        $this->request = htmlspecialchars_decode($this->request);
        $this->request = json_decode($this->request, true);
        
        // TOKEN - THIS CANNOT BE CHANGED!!! -------------------------------------------------------------------------- //
        if (!isset($this->request[0]["tok"])) {
            $this->requestError  = "Your application has no permission to do this!";
        } else if ($this->request[0]["tok"] != "c0a6r1l1o9sL6t2h4gjhak7hf3uf9h2jnkjdq37qh2jk3fbr1706") {
            $this->requestError  = "Your application has no permission to do this!";
        } else {
            $this->request = $this->request[1];
        }
        // -------------------------------------------------------------------------- TOKEN - THIS CANNOT BE CHANGED!!! //
        
        /** // Request
         *  req = Resquest/Function
         */
        if (!isset($this->request["req"])) {
            $this->requestError  = "Your application has no permission to do this!";
            $this->method = "";
        } else {
            $this->method = $this->request["req"];
        }
    }
    
    
    public function index() {
        
        $this->getRequest();
        
        $this->connection = env('DB_CONNECTION', 'mysql');
        
        return $this->loadMethod();
    }
    
    
    public function indexPremium() {
        
        $this->getRequest();
        
        $this->premium["sync_tables"] = [
            "condiments",
            "customers",
            "destinations",
            "item_bumps",
            "items",
            "items_recipe",
            "notification_answers",
            "notification_questions",
            "orders"
        ];
        
        if ($this->method == "SYNC") {

            if(in_array($this->request["entity"], $this->premium["sync_tables"])) {
                $this->connection = env('DB_CONNECTION_PREMIUM', 'mysqlPremium');
            }

            $this->response = $this->insertOrUpdateEntityWeb($this->request, $this->response);
            
            return response()->json($this->response);
            
        } else {
            return $this->loadMethod();
        }
    }
    

    public function loadMethod() {
        
        if(!empty($this->requestError)) {
            $this->response[0]["error"]  = $this->requestError;
            return response()->json($this->response);
        }
        
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            switch (strtoupper($this->method)) {
                case "LOGIN":
                    $this->response = $this->login($this->request, $this->response);
                    break;

                case "SYNC":
                    $this->response = $this->insertOrUpdateEntityWeb($this->request, $this->response);
                    break;

                case "GET_SETTINGS":
                    $this->response = $this->getSettings($this->request, $this->response);
                    break;

                case "GET_DEVICES":
                    $this->response = $this->getDevices($this->request, $this->response);
                    break;
                
                case "DEVICE_ONLINE":
                    $this->response = $this->setDeviceOnline($this->request, $this->response);
                    break;

                case "REGISTER_VALIDATION":
                    $this->response = $this->registerValidation($this->request);
                    break;

                case "SMS_ORDER":
                    $this->response = $this->smsOrder($this->request, $this->response);
                    break;

                case "GET_ENTITY":
                    $this->response = $this->getEntities($this->request, $this->response);
                    break;

                case "GET_SERVER_TIME":
                    $this->response = $this->getServerTime($this->request, $this->response);
                    break;
                
                case "DEVICE_REPLACE":
                    $this->response = $this->deviceReplace($this->request, $this->response);
                    break;
                
                default:
                    $this->response[0]["error"] = "Unknown method '{$this->method}'";
                    return response()->json($this->response);
                    break;
            }

            return response()->json($this->response);
        }
        
    }
    
    public function login(array $request, array $response) {
        
        if (!isset($request["username"])) {
            $response[0]["error"]  = "Undefined username.";
            return $response;
        }

        if (!isset($request["password"])) {
            $response[0]["error"]  = "Undefined password.";
            return $response;
        }

        $username = $request["username"];
        $password = $request["password"];
        $device_serial = isset($request["serial"]) ? $request["serial"] : "";
        
        $sql = "SELECT
                    password,
                    store_guid,
                    business_name
                FROM users
                WHERE username = ? AND deleted_at IS NULL";
        
        $result = DB::select($sql, [$username]);
        
        //echo "sql: " . $sql . "|";
        
        if (count($result) > 0) {
            
            $passDataBase = $result[0]->password;
            
            $passMatched = Hash::check($password, $passDataBase);
            
            if ($passMatched) {
                
                if ($this->existDeviceInAnotherStore($result[0]->store_guid, $device_serial)) {
                    $response[0]["error"] = $this->error_exist_device_in_another_store;
                    
                } else {
                    
                    // Check Device App Version
                    $appVersionCode = isset($request["appVersionCode"]) ? $request["appVersionCode"] : 0;
                    $devices = DB::select("SELECT * FROM devices WHERE store_guid = '" . $result[0]->store_guid . "' AND is_deleted = 0");
                    foreach($devices as $device) {
                        $deviceVersionCode = isset($device->app_version_code) ? $device->app_version_code : 0;
                        if($appVersionCode < $deviceVersionCode) {
                            $response[0]["error"] = "This KDS Station needs to be updated. Go to App Store to update the KDS app.";
                        }
                    }
                    
                    if(!isset($response[0]["error"])) {
                        $response[0]["store_guid"] = $result[0]->store_guid;
                        $response[0]["store_name"] = $result[0]->business_name;
                        
                        $sqlStoreKey = "SELECT store_key FROM settings WHERE store_guid = '" .$result[0]->store_guid. "'";
                        $response[0]["store_key"] = DB::select($sqlStoreKey)[0]->store_key;
                    }
                }
                
            } else {
                $response[0]["error"]  = "Password is incorrect.";
            }
            
        } else {
            $response[0]["error"]  = "Username is incorrect.";
        }
        
        return $response;
        
    }
    
    
    public function deviceReplace(array $request, array $response) {
        
        $response[0]["error"] = "";

        if (!isset($request["store_guid"])) {
            $response[0]["error"]  = "Undefined store GUID.";
            return $response;
        }

        if (!isset($request["device_guid"])) {
            $response[0]["error"]  = "Undefined device GUID.";
            return $response;
        }

        if (!isset($request["device_serial"])) {
            $response[0]["error"]  = "Undefined device serial.";
            return $response;
        }

        $store_guid     = $request["store_guid"];
        $device_guid    = $request["device_guid"];
        $device_serial  = $request["device_serial"];
        
        $device = DB::table('devices')->where(['guid' => $device_guid])->first();
        if (isset($device)) {
            if ($this->existDeviceInAnotherStore($store_guid, $device_serial)) {
                $response[0]["error"] = $this->error_exist_device_in_another_store;
                
            } else {
                $sql = "UPDATE devices SET serial = :device_serial WHERE guid = :device_guid";
    
                $result = DB::update($sql, array("device_serial" => $device_serial, "device_guid" => $device_guid));
                
                if (!$result) {
                    $response[0]["error"]  = "Error while updating device.";
                }
            }

        } else {
            $response[0]["error"] = "Device not found.";
        }

        return $response;
        
    }
    
    
    public function insertOrUpdateEntityWeb(array $request, array $response) {
        
        $appVersion = isset($request["appVersion"]) ? $request["appVersion"] : 0;
        $entity = $request["entity"];

        $appVersion = $this->resolveApostrophe($appVersion);
        $entity = $this->resolveApostrophe($entity);

        $data   = $request["data"];
        
        $objGuidArray = array();
        
        $response[0]["error"] = null;

        foreach ($data as $object) {
            
            $func = "UPD"; // Update
            
            $guid = $object['guid'];
            $updt = $object['update_time'];
            
            $sqlCheck   = "SELECT 1 FROM $entity WHERE guid = $guid";
            
            $result     = $this->DB::connection($this->connection)->select($sqlCheck);
            if (count($result) == 0) {
                $func = "INS"; // Insert
            }
            
            if($func == "INS") {
                $sql  = "INSERT INTO $entity ";
                $sql .= "(";
                foreach($object as $key=>$value) {
                    $sql .= "`$key` , ";
                }
                $sql  = rtrim($sql , ", ");
                $sql .= ") VALUES(";
                
            } else {
                $sql = "UPDATE $entity SET ";
            }
            
            foreach($object as $key=>$value) {
                if(!is_array($value)) {
                    
                    if(is_string($value)) {
                        $value = $this->resolveApostrophe($value);
                    }
                    
                    if ($key == "upload_time") {
                        $value = time();
                    }
                    
                    if($func == "INS") {
                        $sql .= "$value , ";
                    } else {
                        if ($key == "guid") {
                            continue;
                        } else if ($key == "license" && $entity == "devices") {
                            continue;
                        }

                        $sql .= "`$key` = $value , ";
                    }
                    
                }
            }
            
            $sql  = rtrim($sql , ", ");
            
            if($func == "INS") {
                $sql .= ")";
            } else {
                $sql .= " WHERE guid = $guid AND (update_time < $updt OR update_time IS NULL OR upload_time < 2)";
            }

            $result = $this->DB::connection($this->connection)->statement($sql);
            
            if ($result) {
                array_push($objGuidArray, $guid);
                
            } else {
                $response[0]["error"]  = "Error trying $func: $sql";
                break;
            }
        }
        
        // On KDS 1.1 version and below "appVersion" parameter is not handled
        if($appVersion < 1.2 && !isset($response[0]["error"])) { 
            $response = $this->DB::connection($this->connection)->select("SELECT * FROM $entity WHERE guid IN (" . implode(",", $objGuidArray) .")");
        }

        return $response;
        
    }
    
    
    public function resolveApostrophe($str) {
        
        $char = "'";
        
        $empty = str_replace($char, "", $str);
        $empty = str_replace(" ", "", $empty);
        $empty = str_replace(",", "", $empty);
        
        if($empty == "") {
            return $str;
        }
        
        $first = substr($str, 0, 1);
        $last  = substr($str, strlen($str)-1, 1);
        
        if($first == $char && $last == $char) {
            $word  = substr($str, 1, strlen($str) -2);
            $str = $char . str_replace($char, "\'", $word) . $char;
        }
        
        return $str;
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
                $sms_done_use_default  = $storeSettings->sms_done_use_default !== null ? $storeSettings->sms_done_use_default : 0;
                
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

                    if (isset($request["order_id"])) {
                        $msg = str_replace("[ORDER_ID]", $request["order_id"], $msg);
                    }

                    if (isset($request["customer_name"])) {
                        $msg = str_replace("[CUSTOMER_NAME]", $request["customer_name"], $msg);
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
                            $create_time = time();
                            $sql = "INSERT INTO sms_order_sent (store_guid, order_guid, order_status, sms_message, create_time)
                                    VALUES('".$request["store_guid"]."', '".$request["order_guid"]."', 
                                        '".$request["order_status"]."', '".addslashes($msg)."', $create_time)";
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
        if (!isset($request["store_guid"])) {
            $response[0]["error"] = "Undefined store GUID";
            return $response;
        }
        
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
        if (!isset($request["store_guid"])) {
            $response[0]["error"] = "Undefined store GUID";
            return $response;
        }

        $sql = "SELECT * FROM devices WHERE store_guid = :store_guid AND `function` <> 'KDSRouter'";

        $min_update_time = 0;
        if (isset($request["min_update_time"])) {            
            $sql .= " AND update_time > :min_update_time";
            $min_update_time = $request["min_update_time"];
            return DB::select($sql, array("store_guid" => $request["store_guid"], "min_update_time" => $min_update_time));
        } else {
            return DB::select($sql, array("store_guid" => $request["store_guid"]));
        }
    }


    public function getEntities(array $request, array $response) {
        if (!isset($request["store_guid"])) {
            $response[0]["error"] = "Undefined store GUID";
            return $response;
        }

        if (!isset($request["entity"])) {
            $response[0]["error"] = "Undefined entity";
            return $response;
        }

        $entity = $request["entity"];
        $store_guid = $request["store_guid"];
        $min_update_time = isset($request["min_update_time"]) ? $request["min_update_time"] : -1;
        
        if (Schema::hasTable($entity) === false) {
            $response[0]["error"] = "Unknown entity '$entity'";
            return $response;
        }

        if (($entity !== "notification_questions") && ($entity !== "notification_answers")) {
            $response[0]["error"] = "Unknown entity '$entity'";
            return $response;
        }

        $sql = "SELECT * FROM $entity WHERE store_guid = :store_guid";

        if ($min_update_time !== -1) {
            $sql .= " AND update_time > :min_update_time";
        } else {
            $sql .= " AND is_deleted != 1";
        }

        $params = [];
        $params["store_guid"] = $store_guid;
        if ($min_update_time !== -1) $params["min_update_time"] = $min_update_time;

        $result = DB::select($sql, $params);
        
        if (count($result) == 0 && !isset($min_update_time)) {
            $created_at = time();

            if ($entity == "notification_questions") {
                $defaultQuestionSQL = "SELECT title, message FROM notification_questions WHERE store_guid = '' limit 1";
                $questionsDefault = DB::select($defaultQuestionSQL);
                
                if(count($questionsDefault) == 0) {
                    $response[0]["error"]  = "System Default Notifications not configured.";
                    return $response;
                }
                
                $defaultQuestion = $questionsDefault[0];
                
                $question = DB::table('notification_questions');

                $data = [
                    'guid'        => Uuid::uuid4(),
                    'title'       => $defaultQuestion->title,
                    'message'     => $defaultQuestion->message,
                    'create_time' => $created_at,
                    'update_time' => $created_at,
                    'store_guid'  => $store_guid
                ];

                $question->insert($data);
                $result = DB::select($sql, $params);

            } else if ($entity == "notification_answers") {
                $defaultAnswersSQL = "SELECT title, message FROM notification_answers WHERE store_guid = ''";
                $AnswersDefault = DB::select($defaultAnswersSQL);

                $questionSQL = "SELECT guid FROM notification_questions WHERE store_guid = :store_guid limit 1";
                $questionsDefault   = DB::select($questionSQL, array("store_guid" => $store_guid));
                
                if(count($questionsDefault) == 0 || count($AnswersDefault) == 0) {
                    $response[0]["error"]  = "System Default Notifications not configured.";
                    return $response;
                }
                
                $questionGuid = $questionsDefault[0]->guid;

                foreach ($AnswersDefault as $answer) {
                    $answersDB = DB::table('notification_answers');

                    $data = [
                        'guid'          => Uuid::uuid4(),
                        'title'         => $answer->title,
                        'message'       => $answer->message,
                        'create_time'   => $created_at,
                        'update_time'   => $created_at,
                        'store_guid'    => $store_guid,
                        'question_guid' => $questionGuid
                    ];

                    $answersDB->insert($data);
                }

                $result = DB::select($sql, $params);
            }
        }

        return $result;

    }
    
    
    public function registerValidation(Request $request) {
        $return = array();
                
        // -- Email ----------------------------------------------------------------------------------------------------- -- //
        $user = DB::table('users')->where('id', '<>', $request->id)->where('email', '=', $request->email)->first();
        
        if (isset($user)) {
            
            if (isset($user->email)) {
                $return["FIELD"] = "email";
                $return["ERROR"] = "This email is already in use.";
            }
            
        } else if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            
            $return["FIELD"] = "email";
            $return["ERROR"] = "This email is not valid.";
        }
        // -- ----------------------------------------------------------------------------------------------------- Email -- //
        
        if (count($return) == 0) {
            $user = DB::table('users')->where('id', '<>', $request->id)->where('username', '=', $request->username)->first();
            if (isset($user)) {
                if (isset($user->username)) {
                    $return["FIELD"] = "username";
                    $return["ERROR"] = "This username is already in use.";
                }
            }
        }

        if(count($return) == 0 && isset($request->obj)) {
            
            if ($request->obj == 'store') {
                
                $defaultPlan = Plan::where([['delete_time', '=', 0], ['owner_id', '=', $request->parent_id], ['default', '=', 1]])->get()->first();
                if(empty($defaultPlan)) {
                    $return["FIELD"] = "parent_id";
                    $return["ERROR"] = "This StoreGroup does not have a default Plan.";
                }

                if (!isset($request->user_apps)) {
                    $return["FIELD"] = "user_apps";
                    $return["ERROR"] = "Please fill the \"App\" field.";
                    
                } else if (!isset($request->user_envs)) {
                    
                    $return["FIELD"] = "user_envs";
                    $return["ERROR"] = "Please fill the \"Type\" field.";
                }

            }
            
        }
        
        return array($return);
    }
    
    
    public function setDeviceOnline(array $request, array $response) {
        $deviceUpdated = array();
        
        if (!isset($request["store_guid"])) {
            $response[0]["error"] = "Undefined store GUID";
            return $response;
        }

        if (!isset($request["last_connection_time"])) {
            $response[0]["error"] = "Undefined last_connection_time";
            return $response;
        }

        $storeGuid = $request["store_guid"];
        $lastConnectionTime = $request["last_connection_time"];
        if (!is_numeric($lastConnectionTime)) {
            $deviceUpdated[0]["error"]  = "Number expected for last_connection_time";
            return $deviceUpdated;
        }
        
        $device = DB::table('settings')->where('store_guid', '=', $storeGuid)->first();
        if (isset($device)) {
            $update_time = time();
            $sql = "update settings set last_connection_time = :last_connection_time, update_time = :update_time where store_guid = :store_guid";
            $result = DB::update($sql, array("last_connection_time" => $lastConnectionTime, "update_time" => $update_time, "store_guid" => $storeGuid));
            
            if ($result) {
                $deviceUpdated = DB::select("SELECT * FROM settings WHERE store_guid = :store_guid", array("store_guid" => $storeGuid));
            } else {
                $deviceUpdated[0]["error"]  = "Error trying update device. sql: $sql";
            }
        } else {
            $deviceUpdated[0]["error"]  = "Device not found.";
        }
        return $deviceUpdated;
    }
    
    
    public function getServerTime(array $request, array $response) {
        $serverTime = array();
        $serverTime["server_time"] = time();
        return array($serverTime);
    }
    
    
    public function stripslashes_deep($value) {
        $value = is_array($value) ?
        array_map('stripslashes_deep', $value) :
        stripslashes($value);
        
        return $value;
    }


    // Check if the same serial number is activate in another store.
    public function existDeviceInAnotherStore($store_guid, $device_serial) {
        $device = DB::table('users')
            ->join('devices', 'users.store_guid', '=', 'devices.store_guid')
            ->select('devices.guid')
            ->whereNull('users.deleted_at')
            ->where('devices.serial', '=', $device_serial)
            ->where('devices.store_guid', '<>',  $store_guid)
            ->where('devices.is_deleted', '=', 0)
            ->where('devices.split_screen_parent_device_id', '=', 0)
            ->first();

        return isset($device);
    }
    
}





