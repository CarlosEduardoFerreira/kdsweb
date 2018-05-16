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
        $request = $this->stripslashes_deep(html_entity_decode($request));
        //echo "<br>request 2: " . $request;
        $request = json_decode($request, true);
        
        //echo "<br>request 3: " . $request["username"];
        
        $response = array();
        
        /** // Object
         *  obj = Identity on the System/Database
         */
        $obj = $request["obj"];
        
        /** // Request
         *  req = Resquest/Function
         */
        $req = $request["req"];
        
        //echo "<br>: " . "primeiro|";
        
        //echo "post 5: " . $post[$obj] . "|" . $post[$req] . "|" . $post["username"] . "|" . $post["password"];
        
        //echo "json 1: " . $json . "|";
        
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            
            if($obj == "USER") {
                
                //echo "<br>: " . "segundo|";
                
                include "Api/ApiUserController.php";
                $userController = new ApiUserController();
                $response = $userController->login($db, $req, $request, $response);
                
                //echo "<br>: " . "segundo 23|";
                
            } else if($obj == "DEVICE") {
                
                include "device.php";
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
