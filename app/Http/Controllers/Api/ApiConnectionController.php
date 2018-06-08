<?

namespace App\Http\Controllers\Api;

use PDO;

class ApiConnectionController {

    private $pdo;

    public function __construct() {

        $host = "kdsios.cz2l6cajeudq.us-west-2.rds.amazonaws.com";
        $db   = "kdsweb";
        $user = "bematech";
        $pwrd = "%Bematech11714%";

        //$pdo = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8', $user, $pwrd);
        $pdo = new PDO("mysql:host=$host:3306;dbname=$db", $user, $pwrd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;

        //echo "Connected!";

    }

    public function query($query, $params = array()) {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        
        if (explode(' ', $query)[0] == 'SELECT') {
            return $statement;
        }
    }
    
    public function close() {
        $this->pdo = null;
    }

}

?>
