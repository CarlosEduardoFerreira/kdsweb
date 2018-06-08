<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;


class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        // require_once("../log/log.php");
        // $log = new Log();

        $request = file_get_contents("php://input");
        //echo "<br>request 1: " . $request;
        $request = $this->stripslashes_deep(htmlspecialchars_decode($request));
        //echo "<br>request 2: " . $request;
        $request = json_decode($request, true);

        //echo "<br>request 3: " . $request["username"];

        $response = array(array());

        /** // Request
         *  req = Resquest/Function
         */
        $req = $request["req"];

        //echo "<br>: " . "primeiro|";

        //echo "post 5: " . $post[$obj] . "|" . $post[$req] . "|" . $post["username"] . "|" . $post["password"];

        //echo "json 1: " . $json . "|";
// echo "api1 ";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            
            include "ApiConnectionController.php";
            $db = new ApiConnectionController();
            
// echo "api2 ";
            if($req == "SYNC") {
// echo "api3 ";
                $response = ApiSyncController::InsertOrUpdateEntityWeb($db, $request, $response);

            } else if($req == "LOGIN") {

                $response = ApiUserController::login($db, $request, $response);

            } else if($req == "GET_SETTINGS") {
                
                $response = ApiSettingsController::getSettings($db, $request, $response);
                
            } else if($req == "GET_DEVICES") {

                $response = ApiDeviceController::getDevices($db, $request, $response);
                
            }
            
            $db->close();
            
            return response()->json($response);
        }

    }

    public function stripslashes_deep($value) {
        $value = is_array($value) ?
        array_map('stripslashes_deep', $value) :
        stripslashes($value);

        return $value;
    }

}
