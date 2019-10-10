<?php namespace Oxmosys;

use mysqli;
use Oxmosys\InitDB;
use Oxmosys\ClassAutoloader;

class Main
{
    public $dbConnection, $appConfig, $appSession, $appPages;

    public function getAppConfig()
    {
        var_dump($this->appConfig);
        return $this->appConfig;
    }

    public function __construct($appSession)
    {
        // memorizzo il config
        $this->appConfig = json_decode(file_get_contents("php/config/config.json"), true);

        // memorizzo la connessione
        $this->dbConnection = $this->DatabaseConnection();

        // riporto la sessione in main
        $this->appSession = $appSession;
    }

    private function DatabaseConnection()
    {
        // Connetti al database
        $conn = new mysqli($this->appConfig["mysql"]["servername"], $this->appConfig["mysql"]["username"], $this->appConfig["mysql"]["password"]);
        // Se tutto apposto con la connessione inizio la selezione del db, altrimenti do errore
        if ($conn->connect_error) {
            die("Database connection failed: (" . $conn->connect_errno . ") " . $conn->connect_error);
        } else {
            // Se non trovo il database allora lo inizializzo con i moduli necessari, altrimenti lo seleziono
            // Se imposto isinit false nel config posso forzare l'inizializzazione anche in corso d'opera,
            // questo è necessario se si vogliono attivare nuovi moduli!!!
            if (!$conn->select_db($this->appConfig["mysql"]["database"]) || $this->appConfig["mysql"]["isinit"]) {
                $initdb = new InitDB($conn, $this->appConfig);
                $initdb->initAll();
            }
        }

        return $conn;
    }
}
