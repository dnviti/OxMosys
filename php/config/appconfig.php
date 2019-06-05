<?php namespace Oxmosys;

use Exception;
use PDO;
use Oxmosys\InitDB;
use Oxmosys\QueryBuilder;

class AppConfig
{
    public static $config, $dbConn, $session, $qb;

    public function __construct()
    {
        self::$session = session_id();

        if (!self::$config) {
            self::$config = json_decode(file_get_contents("php/config/config.json"), true);
        }

        if (!self::$dbConn) {
            $pdo_connection = self::$config["db"]["type"] . ':host=' . self::$config["db"]["servername"] . ';charset:uft8;';
            self::$dbConn = new PDO($pdo_connection, self::$config["db"]["username"], self::$config["db"]["password"]);
            self::$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        if (!self::$qb) {
            self::$qb = new QueryBuilder(self::$dbConn);
        }

        self::dbExists();
    }

    public static function dbExists()
    {
        $dbCorrect = strtoupper(self::$config["db"]["database"]);
        $dbExists = self::$qb->run("SELECT upper(SCHEMA_NAME) FROM INFORMATION_SCHEMA.SCHEMATA WHERE UPPER(SCHEMA_NAME) = '$dbCorrect'");
        if (isset($dbExists[0][0]) && (bool)self::$config["db"]["isinit"]) {
            self::$dbConn->exec("USE " . $dbCorrect);
            $dbExists = true;
        } else {
            $dbExists = false;
        }

        return $dbExists;
    }
}
