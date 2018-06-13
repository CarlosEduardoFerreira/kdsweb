<?

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class ApiSyncController extends Controller {

    public static function InsertOrUpdateEntityWeb(array $request, array $response) {

        $entity = $request["entity"];
        $data   = $request["data"];
// print_r($data);
        foreach ($data as $object) {
// echo "api5 |";
// addslashes($object);
// print_r($object);

            $func = "UPD"; // Update

            $sqlCheck   = "SELECT 1 FROM $entity WHERE guid_ = " . $object['guid_'];
            //echo "sqlCheck: $sqlCheck";
            $result     = DB::select($sqlCheck);
            if (count($result) == 0) {
                $func = "INS"; // Insert
            }

            $guid = "";
            $updateTime = 0;

            if($func == "INS") {
                $sql  = "INSERT INTO $entity ";
                $sql .= "(";
                foreach($object as $key=>$value) {
                    $sql .= "$key , ";
                }
                $sql  = rtrim($sql , ", ");
                $sql .= ") VALUES(";

            } else {
                $sql = "UPDATE $entity SET ";
            }

            foreach($object as $key=>$value) {
                if(!is_array($value)) {
                    if($func == "INS") {
                        $sql .= "$value , ";
                    } else {
                        $sql .= "$key = $value , ";
                    }
                }
            }

            $sql  = rtrim($sql , ", ");

            if($func == "INS") {
                $sql .= ")";
            } else {
                $sql .= " WHERE guid_ = " . $object['guid_'] . " AND update_time_ < " . $object['update_time_'];
            }

//             echo "sql: $sql";
            $result = DB::statement($sql);

            if($result) {
                $response["result"]  = "OK";
            } else {
                $response["error"]  = "Error trying $func: $result";
            }

        }

        return $response;

    }

}

?>
