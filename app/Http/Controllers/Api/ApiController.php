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

                $response = ApiSyncController::InsertOrUpdateEntityWeb($request, $response);

            } else if($req == "LOGIN") {

                $response = ApiUserController::login($request, $response);

            } else if($req == "GET_SETTINGS") {
                
                $response = ApiSettingsController::getSettings($request, $response);
                
            } else if($req == "GET_DEVICES") {

                $response = ApiDeviceController::getDevices($request, $response);
                
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
