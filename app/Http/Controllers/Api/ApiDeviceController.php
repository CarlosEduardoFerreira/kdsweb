<?

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;


class ApiDeviceController {

    public static function getDevices(array $request, array $response) {

        $sql = "SELECT * FROM devices WHERE store_guid_ = '" . $request["store_guid_"] . "'";

        //echo "sql: " . $sql . "|";

        return DB::select($sql);

    }

}

?>
