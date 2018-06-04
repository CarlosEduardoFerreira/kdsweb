<?

namespace App\Http\Controllers;


use PDO;

class ApiDeviceController {

    public function getDevices(DB $db, array $request, array $response) {

        $storeGuid = $request["storeGuid"];

        $sql = "SELECT * FROM devices WHERE store_guid_ = '$storeGuid'";

        //echo "sql: " . $sql . "|";
        
        $result = $db->query($sql);

        return $result->fetchAll(PDO::FETCH_ASSOC);

    }

}

?>
