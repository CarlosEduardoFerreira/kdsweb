<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

//         return response()->json(['error' => "DEU CERTOOOOOOOO!"]);

        include "Api/db/ConnectionController.php";
        $db = new DB();

        // require_once("../log/log.php");
        // $log = new Log();

        $request = file_get_contents("php://input");
        //echo "<br>request 1: " . $request;
        $request = $this->stripslashes_deep(htmlspecialchars_decode($request));
        //echo "<br>request 2: " . $request;
        $request = json_decode($request, true);

        //echo "<br>request 3: " . $request["username"];

        $response = array();

        /** // Request
         *  req = Resquest/Function
         */
        $req = $request["req"];

        //echo "<br>: " . "primeiro|";

        //echo "post 5: " . $post[$obj] . "|" . $post[$req] . "|" . $post["username"] . "|" . $post["password"];

        //echo "json 1: " . $json . "|";
// echo "api1 ";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
// echo "api2 ";
            if($req == "SYNC") {
// echo "api3 ";
                include "Api/ApiSyncController.php";
                $sync = new ApiSyncController();
                $response = $sync->sync($db, $request, $response);

            } else if($req == "LOGIN") {

                include "Api/ApiUserController.php";
                $userController = new ApiUserController();
                $response = $userController->login($db, $request, $response);

            }

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
