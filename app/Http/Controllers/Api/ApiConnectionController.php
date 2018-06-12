<?

namespace App\Http\Controllers\Api;

use PDO;

class ApiConnectionController {

    private static $pdo;

    public static function create() {

        $host = "kdsios.cz2l6cajeudq.us-west-2.rds.amazonaws.com";
        $db   = "kdsweb";
        $user = "bematech";
        $pwrd = "%Bematech11714%";

        $pdo = new PDO("mysql:host=$host:3306;dbname=$db", $user, $pwrd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo = $pdo;

        //echo "Connected!";

    }

    public function query($query, $params = array()) {
        $statement = self::$pdo->prepare($query);
        $statement->execute($params);
        
        if (explode(' ', $query)[0] == 'SELECT') {
            return $statement;
        }
    }
    
    public function close() {
        self::$pdo = null;
    }

}

?>
