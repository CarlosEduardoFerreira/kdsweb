<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// Even eclipse shows the errors below we need keep this, at least laravel will not work.
use App\Http\Controllers\Api;
use App\Http\Controllers\Api\ApiSyncController;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\ApiSettingsController;
use App\Http\Controllers\Api\ApiDeviceController;


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

                $syncController = new ApiSyncController();
                $response = $syncController::InsertOrUpdateEntityWeb($request, $response);

            } else if($req == "LOGIN") {

                $userController = new ApiUserController();
                $response = $userController::login($request, $response);

            } else if($req == "GET_SETTINGS") {
                
                $settingsController = new ApiSettingsController();
                $response = $settingsController::getSettings($request, $response);
                
            } else if($req == "GET_DEVICES") {

                $deviceController = new ApiDeviceController();
                $response = $deviceController::getDevices($request, $response);
                
            }
            
//             $db->close();
            
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
