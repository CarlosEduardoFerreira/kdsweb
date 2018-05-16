<?

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use PDO;

class ApiUserController {

/** User DEVICE ************************************************************************/


    public function login(DB $db, string $req, array $request, array $response) {
        
        //echo "<br>: " . "terceiro";
        if($req == "LOGIN") {
            
            $username = $request["username"];
            $password = $request["password"];
            
            $sql = "SELECT password, licenses_quantity FROM users  
                    WHERE username = '$username'";
            
            //echo "sql: " . $sql . "|";
            //echo "<br>: " . "quarto";
            $result = $db->query($sql);
        
            if ($row = $result->fetch(PDO::FETCH_ASSOC)) {

                $passDataBase = $row['password'];
                
                $passMatched = Hash::check($password, $passDataBase);
                
                if ($passMatched) {
                    
                    if(isset($row["licenses_quantity"])) {
                        $response["licensesQuantity"] = $row["licenses_quantity"];
                        
                    } else {
                        $response["licensesQuantity"] = 0;
                    }
                    
                } else {
                    
                    $response["error"]  = "Password is incorrect.";
                }
                
            } else {
                
                $response["error"]  = "Username is incorrect.";
            }
        
        }
        
        return $response;

    }
    
    // /** User CHECK *************************************************************************/
        
    // }else if($json[$req] == "CHECK") {
        
    //     $phone = $post->phone;
        
    //     // log
    //     $log->log("BidAPI", "BidAPI.php", "phone",  $phone);
        
    //     $stmt = $db->query("SELECT status FROM user WHERE phone = '$phone'");
        
    //     $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     /**
    //      *  status                      when                        field
    //      *  1 = register_code sent      after set phone number      (set register_code_sent_time)
    //      *  2 = pending password        after set name, email       (set create_time)
    //      *  3 = enable                  after set password          (set enable_time)
    //      *  4 = disable
    //      */
            
    //     if(isset($row["status"])){
            
    //         $json["status"] = $row["status"];
            
    //     }else{
            
    //         // Send Message SMS to the phone number with the code to be validate
    //         $sms = new ManagerSMS();
    //         $code = $sms->sendSMS($phone);
            
    //         // log
    //         $log->log("BidAPI", "BidAPI.php", "code", $code);
            
    //         $json["error"] = $db->query("INSERT INTO user(
    //                                                     phone,
    //                                                     status,
    //                                                     register_code,
    //                                                     register_code_sent_time
    //                                                 )VALUES(
    //                                                     '$phone',
    //                                                     1,
    //                                                     md5('$code'),
    //                                                     ROUND(UNIX_TIMESTAMP(CURTIME(4)) * 1000)
    //                                                 )");
    //         $json["status"] = 1;
            
    //     }
        
    // /** User REGISTER *********************************************************************/
        
    // }else if($json[$req] == "REGISTER"){
        
    //     $phone  = $post->phone;
    //     $serial = $post->serial;
    //     $code   = $post->code;
    //     $name   = $post->name;
    //     $email  = $post->email;
        
    //     $sql = "SELECT status FROM user WHERE phone = '$phone' AND register_code = md5('$code')";
        
    //     // log
    //     $log->log("BidAPI", "BidAPI.php", "sql", $sql);
        
    //     $stmt = $db->query($sql);
        
    //     $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     if(isset($row["status"])){
            
    //         $json["status"] = $row["status"];
            
    //         if($json["status"] == 1){
                
    //             $sql = "UPDATE user SET
    //                                   serial_number = md5('$serial'),
    //                                            name = '$name',
    //                                           email = '$email',
    //                                          status = 2,
    //                                     create_time = ROUND(UNIX_TIMESTAMP(CURTIME(4)) * 1000)
    //                                 WHERE
    //                                     phone = '$phone'
    //                                     AND register_code = md5('$code')";
                
    //             // log
    //             $log->log("BidAPI", "BidAPI.php", "sql", $sql);
                
    //             $json["error"] = $db->query($sql);
                
    //             $json["status"] = 2;
    //         }
            
    //     } else {
    //         $json["error"]  = "This code is invalid.";
    //     }
        
    // /** User SETPWRD *************************************************************************/
        
    // }else if($json[$req] == "SETPWRD"){
        
    //     $phone  = $post->phone;
    //     $pwrd   = $post->pwrd;
        
    //     $sql = "SELECT status FROM user WHERE phone = '$phone'";
        
    //     // log
    //     $log->log("BidAPI", "BidAPI.php", "sql", $sql);
        
    //     $stmt = $db->query($sql);
        
    //     $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     if(isset($row["status"])){
            
    //         $json["status"] = $row["status"];
            
    //         if($json["status"] == 2){
                
    //             $sql = "UPDATE user SET 
    //                                     pwrd    = md5('$pwrd'),
    //                                     status  = 3,
    //                                     enable_time = ROUND(UNIX_TIMESTAMP(CURTIME(4)) * 1000) 
    //                                 WHERE 
    //                                     phone = '$phone'";
                
    //             // log
    //             $log->log("BidAPI", "BidAPI.php", "sql", $sql);
                
    //             $json["error"] = $db->query($sql);
                
    //             $json["status"] = 3;
    //         }
            
    //     } else {
    //         $json["error"]  = "This phone number is not registered.";
    //     }
        
    // /** User AUTH *************************************************************************/
        
    // }else if($json[$req] == "AUTH"){
        
    //     $phone  = $post->phone;
    //     $pwrd   = $post->pwrd;
        
    //     $sql = "SELECT status FROM user WHERE phone = '$phone' AND pwrd = md5('$pwrd')";
        
    //     // log
    //     $log->log("BidAPI", "BidAPI.php", "sql", $sql);
        
    //     $stmt = $db->query($sql);
        
    //     $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     if(isset($row["status"])){
            
    //         $json["status"] = $row["status"];
            
    //         if($json["status"] == 3){
                
    //             $sql = "UPDATE user SET
    //                                     last_login_time = ROUND(UNIX_TIMESTAMP(CURTIME(4)) * 1000) 
    //                                 WHERE
    //                                     phone = '$phone'";
                
    //             // log
    //             $log->log("BidAPI", "BidAPI.php", "sql", $sql);
                
    //             $json["error"] = $db->query($sql);
    //         }
            
    //     } else {
    //         $json["error"]  = "This phone number is not registered.";
    //     }
        
    // }else {
    //     http_response_code(400);
    // }

}

?>




