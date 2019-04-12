<?php namespace Oxmosys;

class Modules extends Main
{
    public function __initLicensingModule()
    { }

    public function __initUsersModule()
    {
        // genero la tabella dei ruoli utente
        $this->tablesStatus["app_user_roles"] =
            AppConfig::$dbConn->query("CREATE TABLE `app_user_roles` 
                            (
                                `id` INT NOT NULL, 
                                `description` VARCHAR(255) NOT NULL,
                                `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                PRIMARY KEY (`id`)
                            ) ENGINE = InnoDB
        ");

        // Inserisco I valori solo se la tabella è stata appena creata
        if ($this->tablesStatus["app_user_roles"]) {
            $queryDefaultUserRoles = [
                "INSERT INTO `app_user_roles` (`id`, `description`) VALUES (0, 'Super Admin')",
                "INSERT INTO `app_user_roles` (`id`, `description`) VALUES (1, 'Admin')",
                "INSERT INTO `app_user_roles` (`id`, `description`) VALUES (2, 'Moderator')",
                "INSERT INTO `app_user_roles` (`id`, `description`) VALUES (3, 'User')"
            ];

            // Imposto tutte le configurazioni di default
            foreach ($queryDefaultUserRoles as $key => $value) {
                AppConfig::$dbConn->query($value);
            }
        }

        // genero la tabella utenti standard
        $this->tablesStatus["app_users"] =
            AppConfig::$dbConn->query("CREATE TABLE `app_users` 
                                (
                                    `id` INT NOT NULL AUTO_INCREMENT, 
                                    `name` VARCHAR(255) NOT NULL, 
                                    `surname` VARCHAR(255) NOT NULL, 
                                    `username` VARCHAR(255) NOT NULL, 
                                    `password` VARCHAR(255) NOT NULL, 
                                    `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `obsolete` TINYINT NOT NULL DEFAULT 0,
                                    `app_user_role_id` INT NOT NULL DEFAULT '1',
                                    PRIMARY KEY (`id`),
                                    UNIQUE `idx_app_users_username` (`username`),
    
                                    CONSTRAINT fk_app_user_roles FOREIGN KEY (app_user_role_id)
                                    REFERENCES app_user_roles(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION
                                ) ENGINE = InnoDB
            ");

        // Inserisco I valori solo se la tabella è stata appena creata
        if ($this->tablesStatus["app_users"]) {
            $adminPassword = '$2y$10$WeX4wUGjx9fMvOnEQQUQne2NuIUfLcq5l9P8/k/7YbzMlB3Y8ATn.'; // default password: admin
            AppConfig::$dbConn->query("INSERT INTO `app_users` (`id`, `name`, `surname`, `username`, `password`, `app_user_role_id`) VALUES (1, 'admin', 'admin', 'admin', '$adminPassword', 0)");
        }
    }

    public function __initSuppliersModule()
    {
        // genero la tabella fornitori standard
        $this->tablesStatus["app_suppliers"] =
            $this->conn->query("CREATE TABLE `app_suppliers` 
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
                        `notes` VARCHAR(255) NULL DEFAULT '',
                        PRIMARY KEY (`id`),

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
    }

    public function __initWarehouseModule()
    {
        // genero la tabella di anagrafica dei magazzini
        $this->tablesStatus["app_warehouses"] =
            AppConfig::$dbConn->query("CREATE TABLE `app_warehouses` 
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
                                    `notes` VARCHAR(255) NULL DEFAULT '',
                                    PRIMARY KEY (`id`),
    
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


        // genero la tabella anagrafica prodotti
        $this->tablesStatus["app_warehouse_items"] =
            AppConfig::$dbConn->query("CREATE TABLE `app_warehouse_items` 
                                (
                                    `id` INT NOT NULL AUTO_INCREMENT, 
                                    `code` VARCHAR(255) NOT NULL, 
                                    `descri` VARCHAR(255) NOT NULL, 
                                    `unitprice` INT NOT NULL, 
                                    `address` VARCHAR(255) NOT NULL,
                                    `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                                    `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `userreg` INT NOT NULL,
                                    `userupdate` INT NOT NULL,
                                    `obsolete` TINYINT NOT NULL DEFAULT 0,
                                    `app_supplier_id` INT NOT NULL, 
                                    `app_warehouse_id` INT NOT NULL,
                                    `notes` VARCHAR(255) NULL DEFAULT '',
                                    PRIMARY KEY (`id`),
    
                                    CONSTRAINT fk_app_warehouse_items_userreg FOREIGN KEY (userreg)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION,
    
                                    CONSTRAINT fk_app_warehouse_items_userupdate FOREIGN KEY (userupdate)
                                    REFERENCES app_users(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION,
    
                                    CONSTRAINT fk_app_warehouse_items_warehouse FOREIGN KEY (app_warehouse_id)
                                    REFERENCES app_warehouses(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION,
    
                                    CONSTRAINT fk_app_warehouse_items_supplier FOREIGN KEY (app_supplier_id)
                                    REFERENCES app_suppliers(id)
                                    ON DELETE NO ACTION
                                    ON UPDATE NO ACTION
                                ) ENGINE = InnoDB
            ");



        // genero la tabella anagrafica causali magazzino
        $this->tablesStatus["app_warehouse_causals"] =
            AppConfig::$dbConn->query("CREATE TABLE `app_warehouse_causals` 
                                (
                                    `id` INT NOT NULL AUTO_INCREMENT, 
                                    `code` VARCHAR(255) NOT NULL, 
                                    `descri` VARCHAR(255) NOT NULL,
                                    `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                                    `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `userreg` INT NOT NULL,
                                    `userupdate` INT NOT NULL,
                                    `notes` VARCHAR(255) NULL DEFAULT '',
                                    PRIMARY KEY (`id`),
    
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
            AppConfig::$dbConn->query("CREATE TABLE `app_warehouse_movements` 
                    (
                        `id` INT NOT NULL AUTO_INCREMENT, 
                        `descri` VARCHAR(255) NULL DEFAULT NULL, 
                        `quantity` INT NOT NULL, 
                        `address` VARCHAR(255) NOT NULL,
                        `datereg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                        `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `userreg` INT NOT NULL,
                        `userupdate` INT NOT NULL,
                        `app_supplier_id` INT NOT NULL, 
                        `app_warehouse_id` INT NOT NULL,
                        `notes` VARCHAR(255) NULL DEFAULT '',
                        PRIMARY KEY (`id`),
    
                        CONSTRAINT fk_app_warehouse_movements_userreg FOREIGN KEY (userreg)
                        REFERENCES app_users(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
    
                        CONSTRAINT fk_app_warehouse_movements_userupdate FOREIGN KEY (userupdate)
                        REFERENCES app_users(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
    
                        CONSTRAINT fk_app_warehouse_movements_warehouse FOREIGN KEY (app_warehouse_id)
                        REFERENCES app_warehouses(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
    
                        CONSTRAINT fk_app_warehouse_movements_supplier FOREIGN KEY (app_supplier_id)
                        REFERENCES app_suppliers(id)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION
                    ) ENGINE = InnoDB
            ");
    }
}
