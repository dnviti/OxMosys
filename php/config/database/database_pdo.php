<?php namespace Oxmosys;

use PDO;
use mysqli;

class PDODB
{

    //private static $dbConfig;

    private $_connection;
    // Store the single instance.
    private static $_instance;

    // Get an instance of the Database.
    // @return Database: 
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self(self::dbConfig);
        }
        return self::$_instance;
    }

    // Constructor - Build the PDO Connection:
    public function __construct($appConfig)
    {
        $dbConfig = $appConfig["db"];

        $db_options = array(
            /* important! use actual prepared statements (default: emulate prepared statements) */
            PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        $this->_connection = new PDO('mysql:host=' . $dbConfig["servername"] . ';dbname=' . $dbConfig["database"] . ';charset=utf8', $dbConfig["username"], $dbConfig["password"], $db_options);
    }

    // Empty clone magic method to prevent duplication:
    private function __clone()
    { }

    // Get the PDO connection:    
    public function getConnection()
    {
        return $this->_connection;
    }
}
