<?

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use PDO;

class ApiUserController {

    public function login(DB $db, array $request, array $response) {

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

        return $response;

    }

}

?>
