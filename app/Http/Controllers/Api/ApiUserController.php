<?

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\Hash;
use PDO;

class ApiUserController {

    public static function login(ApiConnectionController $db, array $request, array $response) {

        $username = $request["username"];
        $password = $request["password"];

        $sql = "SELECT 
                    password,
                    store_guid 
                FROM users
                WHERE username = '$username'";

        //echo "sql: " . $sql . "|";
        
        $result = $db->query($sql);

        if ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $passDataBase = $row['password'];

            $passMatched = Hash::check($password, $passDataBase);

            if ($passMatched) {
                
                $response[0]["store_guid_"] = $row["store_guid"];
                
                $request["store_guid_"]     = $row["store_guid"];
                
                // include store settings on response
                $response = ApiSettingsController::getSettings($db, $request, $response);

            } else {

                $response[0]["error"]  = "Password is incorrect.";
            }

        } else {

            $response[0]["error"]  = "Username is incorrect.";
        }

        return $response;

    }

}

?>
