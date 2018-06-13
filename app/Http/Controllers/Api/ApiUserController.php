<?

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
// Even eclipse shows the error below we need keep this, at least laravel will not work.
use App\Http\Controllers\Api;
use App\Http\Controllers\Api\ApiSettingsController;

class ApiUserController extends Controller {

    public static function login(array $request, array $response) {

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
                
                $response[0]["store_guid_"] = $result[0]->store_guid;
                
                $request["store_guid_"]     = $result[0]->store_guid;
                
                // include store settings on response
                $response = ApiSettingsController::getSettings($request, $response);

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
