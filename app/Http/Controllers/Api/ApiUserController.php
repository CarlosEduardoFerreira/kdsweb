<?

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use PDO;

class ApiUserController {

    public function login(DB $db, array $request, array $response) {

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
                
                $response["storeGuid"] = $row["store_guid"];
                
                $licSQL = "SELECT licenses_quantity_ FROM settings WHERE store_guid_ = '" . $response["storeGuid"] . "'";
                $licRes = $db->query($licSQL);
                
                if ($licRow = $licRes->fetch(PDO::FETCH_ASSOC)) {
                    if(isset($licRow["licenses_quantity_"])) {
                        $response["licensesQuantity"] = $licRow["licenses_quantity_"];
                        
                    } else {
                        $response["licensesQuantity"] = 0;
                    }
                }

            } else {

                $response["error"]  = "Password is incorrect.";
            }

        } else {

            $response["error"]  = "Username is incorrect.";
        }

        return $response;

    }

}

?>
