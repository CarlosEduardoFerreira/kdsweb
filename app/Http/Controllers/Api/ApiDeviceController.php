<?

namespace App\Http\Controllers\Api;


use PDO;

class ApiDeviceController {

    public static function getDevices(ApiConnectionController $db, array $request, array $response) {

        $sql = "SELECT * FROM devices WHERE store_guid_ = '" . $request["store_guid_"] . "'";

        //echo "sql: " . $sql . "|";
        
        $result = $db->query($sql);

        return $result->fetchAll(PDO::FETCH_ASSOC);

    }

}

?>
