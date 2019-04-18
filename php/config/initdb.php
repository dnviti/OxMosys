<?php namespace Oxmosys;

//include "php/config/requires.php";

use Exception;
use Oxmosys\QueryBuilder;
use PDO;
use Oxmosys\AppConfig;

class InitDB
{
    private static $appDbConn, $appConfig, $queryBuilder;
    private $tablesStatus = array();

    public function __construct()
    {
        $app = new AppConfig();
        self::$appConfig = $app::$config;
        self::$appDbConn = $app::$dbConn;
        self::$queryBuilder = new QueryBuilder(self::$appDbConn);
    }

    private function initDefault()
    {
        try {

            $dbExists = self::$queryBuilder->run("SELECT upper(SCHEMA_NAME) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . self::$appConfig["db"]["database"] . "'");

            if (isset($dbExists[0][0])) {
                if ($dbExists[0][0] == strtoupper(self::$appConfig["db"]["database"])) {
                    self::$queryBuilder->run("DROP DATABASE " . self::$appConfig["db"]["database"]);
                }
            }

            self::$appDbConn->query("CREATE DATABASE " . self::$appConfig["db"]["database"]);
            self::$appDbConn->query("USE " . self::$appConfig["db"]["database"]);

            // genero la tabella dei ruoli utente
            $this->tablesStatus["app_user_roles"] =
                self::$appDbConn->query("CREATE TABLE `app_user_roles` 
                                (
                                    `id` INT NOT NULL, 
                                    `descri` VARCHAR(255) NOT NULL,
                                    `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                                ) ENGINE = InnoDB
            ");

            // Inserisco I valori solo se la tabella è stata appena creata
            if ($this->tablesStatus["app_user_roles"]) {
                // $queryDefaultUserRoles = [
                //     "INSERT INTO `app_user_roles` (`id`, `description`) VALUES (0, 'Super Admin')",
                //     "INSERT INTO `app_user_roles` (`id`, `description`) VALUES (1, 'Admin')",
                //     "INSERT INTO `app_user_roles` (`id`, `description`) VALUES (2, 'Moderator')",
                //     "INSERT INTO `app_user_roles` (`id`, `description`) VALUES (3, 'User')"
                // ];

                $queryDefaultUserRoles = [
                    array('id' => 0, 'descri' => 'Super Admin'),
                    array('id' => 1, 'descri' => 'Admin'),
                    array('id' => 2, 'descri' => 'Moderatore'),
                    array('id' => 3, 'descri' => 'Utente')
                ];

                // Imposto tutte le configurazioni di default
                foreach ($queryDefaultUserRoles as $key => $value) {
                    self::$queryBuilder
                        ->table('app_user_roles')
                        ->insert($queryDefaultUserRoles[$key])
                        ->run();

                    //self::$appDbConn->query($value);
                }
            }

            // genero la tabella utenti standard
            $this->tablesStatus["app_users"] =
                self::$appDbConn->query("CREATE TABLE `app_users` 
                                (
                                    `id` INT NOT NULL AUTO_INCREMENT, 
                                    `name` VARCHAR(255) NOT NULL, 
                                    `surname` VARCHAR(255) NOT NULL, 
                                    `username` VARCHAR(255) NOT NULL, 
                                    `password` VARCHAR(255) NOT NULL, 
                                    `email` VARCHAR(255) NOT NULL, 
                                    `notes` VARCHAR(255) NULL DEFAULT NULL, 
                                    `userreg` INT NOT NULL, 
                                    `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `obsolete` TINYINT NOT NULL DEFAULT 0,
                                    `app_user_roles_id` INT NOT NULL DEFAULT 3,
                                    PRIMARY KEY (`id`),
                                    UNIQUE `idx_app_users_username` (`username`),

                                    INDEX fk_app_user_roles (app_user_roles_id),
    
                                    CONSTRAINT fk_app_user_roles FOREIGN KEY (app_user_roles_id)
                                    REFERENCES app_user_roles(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION
                                ) ENGINE = InnoDB
            ");

            // Inserisco I valori solo se la tabella è stata appena creata
            if ($this->tablesStatus["app_users"]) {
                $adminPassword = '$2y$10$WeX4wUGjx9fMvOnEQQUQne2NuIUfLcq5l9P8/k/7YbzMlB3Y8ATn.'; // default password: admin
                self::$appDbConn->query("INSERT INTO `app_users` (`id`, `name`, `surname`, `username`, `password`, `userreg`, `app_user_roles_id`) VALUES (1, 'admin', 'admin', 'admin', '$adminPassword', 1, 0)");
            }



            // genero la tabella fornitori standard
            $this->tablesStatus["app_suppliers"] =
                self::$appDbConn->query("CREATE TABLE `app_suppliers` 
                                (
                                    `id` INT NOT NULL AUTO_INCREMENT, 
                                    `code` VARCHAR(255) NOT NULL DEFAULT '0', 
                                    `buname` VARCHAR(255) NOT NULL, 
                                    `vatid` VARCHAR(255) NOT NULL, 
                                    `telephone` VARCHAR(255) NULL DEFAULT NULL, 
                                    `address` VARCHAR(255) NOT NULL, 
                                    `email` VARCHAR(255) NULL DEFAULT NULL,
                                    `userreg` INT NOT NULL,
                                    `userupdate` INT NOT NULL,
                                    `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `obsolete` TINYINT NOT NULL DEFAULT 0,
                                    `notes` VARCHAR(255) NULL DEFAULT NULL,
                                    PRIMARY KEY (`id`),

                                    INDEX fk_app_suppliers_userreg (userreg),
                                    INDEX fk_app_suppliers_userupdate (userupdate),
    
                                    CONSTRAINT fk_app_suppliers_userreg FOREIGN KEY (userreg)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION,
    
                                    CONSTRAINT fk_app_suppliers_userupdate FOREIGN KEY (userupdate)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION
                                ) ENGINE = InnoDB
            ");




            // genero la tabella di anagrafica dei magazzini
            $this->tablesStatus["app_warehouses"] =
                self::$appDbConn->query("CREATE TABLE `app_warehouses` 
                                (
                                    `id` INT NOT NULL AUTO_INCREMENT, 
                                    `code` VARCHAR(255) NOT NULL, 
                                    `descri` VARCHAR(255) NOT NULL, 
                                    `country` VARCHAR(255) NOT NULL DEFAULT 'IT', 
                                    `address` VARCHAR(255) NOT NULL,
                                    `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                                    `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `userreg` INT NOT NULL,
                                    `userupdate` INT NOT NULL,
                                    `notes` VARCHAR(255) NULL DEFAULT NULL,
                                    `obsolete` INT NOT NULL DEFAULT 0,
                                    PRIMARY KEY (`id`),

                                    INDEX fk_app_warehouses_userreg (userreg),
                                    INDEX fk_app_warehouses_userupdate (userupdate),
    
                                    CONSTRAINT fk_app_warehouses_userreg FOREIGN KEY (userreg)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION,
    
                                    CONSTRAINT fk_app_warehouses_userupdate FOREIGN KEY (userupdate)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION
                                ) ENGINE = InnoDB
            ");

            // genero la tabella anagrafica unità misura
            $this->tablesStatus["app_measure_units"] =
                self::$appDbConn->query("CREATE TABLE `app_measure_units` 
                                    (
                                        `id` INT NOT NULL AUTO_INCREMENT, 
                                        `code` VARCHAR(255) NOT NULL, 
                                        `descri` VARCHAR(255) NOT NULL,
                                        `notes` VARCHAR(255) NULL DEFAULT NULL,
                                        PRIMARY KEY (`id`)
                                    ) ENGINE = InnoDB
                            ");

            if ($this->tablesStatus["app_measure_units"]) {
                self::$appDbConn->query("INSERT INTO `app_measure_units` (`id`, `code`, `descri`) VALUES (1, 'NUMBER', 'Numero')");
            }


            // genero la tabella anagrafica prodotti
            $this->tablesStatus["app_warehouse_items"] =
                self::$appDbConn->query("CREATE TABLE `app_warehouse_items` 
                                (
                                    `id` INT NOT NULL AUTO_INCREMENT, 
                                    `code` VARCHAR(255) NOT NULL, 
                                    `descri` VARCHAR(255) NOT NULL, 
                                    `unitprice` DOUBLE NOT NULL, 
                                    `app_measure_units_id` INT NOT NULL DEFAULT 1, 
                                    `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                                    `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `userreg` INT NOT NULL,
                                    `userupdate` INT NOT NULL,
                                    `obsolete` TINYINT NOT NULL DEFAULT 0,
                                    `app_suppliers_id` INT NOT NULL, 
                                    `app_warehouses_id` INT NOT NULL,
                                    `notes` VARCHAR(255) NULL DEFAULT NULL,
                                    PRIMARY KEY (`id`),

                                    INDEX fk_app_warehouse_items_userreg (userreg),
                                    INDEX fk_app_warehouse_items_userupdate (userupdate),
                                    INDEX fk_app_warehouse_items_warehouse (app_warehouses_id),
                                    INDEX fk_app_warehouse_items_supplier (app_suppliers_id),
    
                                    CONSTRAINT fk_app_warehouse_items_userreg FOREIGN KEY (userreg)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION,
    
                                    CONSTRAINT fk_app_warehouse_items_userupdate FOREIGN KEY (userupdate)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION,
    
                                    CONSTRAINT fk_app_warehouse_items_warehouse FOREIGN KEY (app_warehouses_id)
                                    REFERENCES app_warehouses(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION,
    
                                    CONSTRAINT fk_app_warehouse_items_supplier FOREIGN KEY (app_suppliers_id)
                                    REFERENCES app_suppliers(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION
                                ) ENGINE = InnoDB
            ");



            // genero la tabella anagrafica causali magazzino
            $this->tablesStatus["app_warehouse_causals"] =
                self::$appDbConn->query("CREATE TABLE `app_warehouse_causals` 
                                (
                                    `id` INT NOT NULL AUTO_INCREMENT, 
                                    `code` VARCHAR(255) NOT NULL, 
                                    `type` CHAR(1) NOT NULL,
                                    `descri` VARCHAR(255) NOT NULL,
                                    `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                                    `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `userreg` INT NOT NULL,
                                    `userupdate` INT NOT NULL,
                                    `notes` VARCHAR(255) NULL DEFAULT NULL,
                                    PRIMARY KEY (`id`),

                                    INDEX fk_app_warehouse_causals_userreg (userreg),
                                    INDEX fk_app_warehouse_causals_userupdate (userupdate),
    
                                    CONSTRAINT fk_app_warehouse_causals_userreg FOREIGN KEY (userreg)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION,
    
                                    CONSTRAINT fk_app_warehouse_causals_userupdate FOREIGN KEY (userupdate)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION
                                ) ENGINE = InnoDB
            ");


            // genero la tabella movimenti magazzino
            $this->tablesStatus["app_warehouse_movements"] =
                self::$appDbConn->query("CREATE TABLE `app_warehouse_movements` 
                    (
                        `id` INT NOT NULL AUTO_INCREMENT, 
                        `app_warehouse_items_id` INT NOT NULL,
                        `descri` VARCHAR(255) NULL DEFAULT NULL, 
                        `quantity` INT NOT NULL,
                        `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                        `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `userreg` INT NOT NULL,
                        `userupdate` INT NOT NULL,
                        `app_warehouse_causals_id` INT NOT NULL, 
                        `app_suppliers_id` INT NOT NULL, 
                        `app_warehouses_id` INT NOT NULL,
                        `notes` VARCHAR(255) NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),

                        INDEX fk_app_warehouse_movements_warehouse_causal (app_warehouse_causals_id),
                        INDEX fk_app_warehouse_movements_userreg (userreg),
                        INDEX fk_app_warehouse_movements_userupdate (userupdate),
                        INDEX fk_app_warehouse_movements_warehouse (app_warehouses_id),
                        INDEX fk_app_warehouse_movements_supplier (app_suppliers_id),
                        INDEX fk_app_warehouse_movements_item (app_warehouse_items_id),
    
                        CONSTRAINT fk_app_warehouse_movements_warehouse_causal FOREIGN KEY (app_warehouse_causals_id)
                        REFERENCES app_warehouse_causals(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
                        
                        CONSTRAINT fk_app_warehouse_movements_userreg FOREIGN KEY (userreg)
                        REFERENCES app_users(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
    
                        CONSTRAINT fk_app_warehouse_movements_userupdate FOREIGN KEY (userupdate)
                        REFERENCES app_users(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
    
                        CONSTRAINT fk_app_warehouse_movements_warehouse FOREIGN KEY (app_warehouses_id)
                        REFERENCES app_warehouses(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
    
                        CONSTRAINT fk_app_warehouse_movements_supplier FOREIGN KEY (app_suppliers_id)
                        REFERENCES app_suppliers(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
    
                        CONSTRAINT fk_app_warehouse_movements_item FOREIGN KEY (app_warehouse_items_id)
                        REFERENCES app_warehouse_items(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION
                    ) ENGINE = InnoDB
            ");

            // genero la tabella dei tipi report
            $this->tablesStatus["app_reports_type"] =
                self::$appDbConn->query("CREATE TABLE `app_reports_type` 
                    (
                        `id` INT(11) NOT NULL,
                        `descri` VARCHAR(255) NOT NULL,
                        PRIMARY KEY (id)
                    ) ENGINE = InnoDB
            ");

            // genero la tabella dei report
            $this->tablesStatus["app_reports"] =
                self::$appDbConn->query("CREATE TABLE `app_reports` 
                    (
                        `id` INT(11) NOT NULL,
                        `app_reports_type_id` INT(11) NOT NULL,
                        `outfilename` VARCHAR(255) NOT NULL,
                        `descri` VARCHAR(255) NOT NULL,
                        `query_path` VARCHAR(255) NOT NULL,
                        `inputs` VARCHAR(4000) NULL DEFAULT NULL,
                        PRIMARY KEY (id),

                        INDEX fk_app_reports_type (app_reports_type_id),

                        CONSTRAINT fk_app_reports_type FOREIGN KEY (app_reports_type_id)
                        REFERENCES app_reports_type(id)
                        ON UPDATE NO ACTION
                        ON DELETE NO ACTION
                    ) ENGINE = InnoDB
            ");

            // $this->DBRestore("php/config/database/stdDb.sql");
        } catch (Exception $th) {
            throw $th->__construct("Error on Table " . $this->tablesStatus[sizeof($this->tablesStatus) - 1] . ", ERROR: " . self::$appDbConn->error, 1);
        }
    }

    private function initCustom()
    {

        // genero la tabella anagrafica prodotti
        $this->tablesStatus["app_custom_warehouse_items"] =
            self::$appDbConn->query("CREATE TABLE `app_custom_warehouse_items` 
                            (
                                `app_warehouse_items_id` INT NOT NULL, 
                                `tipo` VARCHAR(255) NOT NULL,
                                `modello` VARCHAR(255) NOT NULL,
                                `colore` VARCHAR(255) NOT NULL,
                                `taglia` VARCHAR(255) NOT NULL,
                                `genere` VARCHAR(255) NOT NULL,
                                `imagepath` VARCHAR(255) NULL DEFAULT NULL,
                                CONSTRAINT fk_app_custom_warehouse_items_userreg FOREIGN KEY (app_warehouse_items_id)
                                REFERENCES app_warehouse_items(id)
                                ON DELETE CASCADE
                                ON UPDATE CASCADE
                            ) ENGINE = InnoDB
        ");

        // Inserisco i dati custom solo se è la prima inizializzazione per la tabella



        // Inserisco il magazzino personalizzato
        if ($this->tablesStatus["app_warehouses"]) {
            self::$appDbConn->query("INSERT INTO `app_warehouses` (`code`, `descri`, `address`, `userreg`, `userupdate`) VALUES ('MAIN', 'Main Warehouse', 'Location', 1, 1)");
        }

        // Inserisco le causali personalizzate
        if ($this->tablesStatus["app_warehouse_causals"]) {
            self::$appDbConn->query("INSERT INTO `app_warehouse_causals` (`id`, `type`, `code`, `descri`, `userreg`, `userupdate`) VALUES (1, '+', 'CARICO_FORNITORE', 'Carico per acquisto merce presso fornitore', 1, 1)");
            self::$appDbConn->query("INSERT INTO `app_warehouse_causals` (`id`, `type`, `code`, `descri`, `userreg`, `userupdate`) VALUES (2, '-', 'SCARICO_NEGOZIO', 'Scarico per vendita al negozio', 1, 1)");
            self::$appDbConn->query("INSERT INTO `app_warehouse_causals` (`id`, `type`, `code`, `descri`, `userreg`, `userupdate`) VALUES (3, '-', 'SCARICO_MATTEW', 'Scarico per vendita su facebook', 1, 1)");
            self::$appDbConn->query("INSERT INTO `app_warehouse_causals` (`id`, `type`, `code`, `descri`, `userreg`, `userupdate`) VALUES (4, '-', 'SCARICO_ECOMMERCE', 'Scarico per vendita su e-commerce', 1, 1)");
            self::$appDbConn->query("INSERT INTO `app_warehouse_causals` (`id`, `type`, `code`, `descri`, `userreg`, `userupdate`) VALUES (5, '-', 'SCARICO_RESO_FORNITORE', 'Scarico da reso fornitore', 1, 1)");
            self::$appDbConn->query("INSERT INTO `app_warehouse_causals` (`id`, `type`, `code`, `descri`, `userreg`, `userupdate`) VALUES (6, '+', 'CARICO_RESO_CLIENTE', 'Carico da reso cliente', 1, 1)");
            self::$appDbConn->query("INSERT INTO `app_warehouse_causals` (`id`, `type`, `code`, `descri`, `userreg`, `userupdate`) VALUES (7, '+', 'CARICO_RETTIFICA_INVENTARIALE', 'Carico rettifica inventario', 1, 1)");
            self::$appDbConn->query("INSERT INTO `app_warehouse_causals` (`id`, `type`, `code`, `descri`, `userreg`, `userupdate`) VALUES (8, '-', 'SCARICO_RETTIFICA_INVENTARIALE', 'Scarico rettifica inventario', 1, 1)");
        }
    }

    private function setInit()
    {
        // Impostare l'init a true sul json
        self::$appConfig["db"]["isinit"] = true;
        $newAppConfig = json_encode(self::$appConfig);
        file_put_contents("php/config/config.json", $newAppConfig);
    }

    public function getInit()
    {
        // leggere l'init dal json
    }
    public function initAll()
    {
        try {
            $this->initDefault();
            $this->initCustom();
            $this->setInit();
        } catch (\Throwable $th) {
            echo $th->getCode() . ";" . $th->getMessage();
        }
    }
}
