<?

namespace App\Http\Controllers;

use PDO;

class DB {
    
    private $pdo;
    
    public function __construct() {
        
        $host = "localhost";
        $db   = "kdsweb";
        $user = "root";
        $pwrd = "1234";
        
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
    
}

?>