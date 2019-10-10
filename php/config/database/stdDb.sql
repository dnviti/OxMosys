CREATE TABLE IF NOT EXISTS `app_suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL DEFAULT '0',
  `buname` varchar(255) NOT NULL,
  `vatid` varchar(255) NOT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `userreg` int(11) NOT NULL,
  `userupdate` int(11) NOT NULL,
  `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `obsolete` tinyint(4) NOT NULL DEFAULT '0',
  `notes` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_app_suppliers_userreg` (`userreg`),
  KEY `fk_app_suppliers_userupdate` (`userupdate`),
  CONSTRAINT `fk_app_suppliers_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_suppliers_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `app_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `obsolete` tinyint(4) NOT NULL DEFAULT '0',
  `app_user_role_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_app_users_username` (`username`),
  KEY `fk_app_user_roles` (`app_user_role_id`),
  CONSTRAINT `fk_app_user_roles` FOREIGN KEY (`app_user_role_id`) REFERENCES `app_user_roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = latin1;


INSERT
  IGNORE INTO `app_users` (
    `id`,
    `name`,
    `surname`,
    `username`,
    `password`,
    `obsolete`,
    `app_user_role_id`
  )
VALUES
  (
    1,
    'admin',
    'admin',
    'admin',
    '$2y$10$WeX4wUGjx9fMvOnEQQUQne2NuIUfLcq5l9P8/k/7YbzMlB3Y8ATn.',
    0,
    0
  );
  
CREATE TABLE IF NOT EXISTS `app_user_roles` (
    `id` int(11) NOT NULL,
    `description` varchar(255) NOT NULL,
    `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE = InnoDB DEFAULT CHARSET = latin1;
  
INSERT
  IGNORE INTO `app_user_roles` (`id`, `description`)
VALUES
  (0, 'Super Admin'),
  (1, 'Admin'),
  (2, 'Moderator'),
  (3, 'User');
  
CREATE TABLE IF NOT EXISTS `app_warehouses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(255) NOT NULL,
    `descri` varchar(255) NOT NULL,
    `country` varchar(255) NOT NULL DEFAULT 'IT',
    `address` varchar(255) NOT NULL,
    `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `userreg` int(11) NOT NULL,
    `userupdate` int(11) NOT NULL,
    `notes` varchar(255) DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `fk_app_warehouses_userreg` (`userreg`),
    KEY `fk_app_warehouses_userupdate` (`userupdate`),
    CONSTRAINT `fk_app_warehouses_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_app_warehouses_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
  ) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = latin1;
  
INSERT
  IGNORE INTO `app_warehouses` (
    `id`,
    `code`,
    `descri`,
    `country`,
    `address`,
    `userreg`,
    `userupdate`,
    `notes`
  )
VALUES
  (
    1,
    'MAIN',
    'Main Warehouse',
    'IT',
    'Location',
    1,
    1,
    ''
  );
  
CREATE TABLE IF NOT EXISTS `app_warehouse_causals` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(255) NOT NULL,
    `descri` varchar(255) NOT NULL,
    `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `userreg` int(11) NOT NULL,
    `userupdate` int(11) NOT NULL,
    `notes` varchar(255) DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `fk_app_warehouse_causals_userreg` (`userreg`),
    KEY `fk_app_warehouse_causals_userupdate` (`userupdate`),
    CONSTRAINT `fk_app_warehouse_causals_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_app_warehouse_causals_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
  ) ENGINE = InnoDB AUTO_INCREMENT = 9 DEFAULT CHARSET = latin1;
  
CREATE TABLE IF NOT EXISTS `app_warehouse_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(255) NOT NULL,
    `descri` varchar(255) NOT NULL,
    `unitprice` int(11) NOT NULL,
    `address` varchar(255) NOT NULL,
    `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `userreg` int(11) NOT NULL,
    `userupdate` int(11) NOT NULL,
    `obsolete` tinyint(4) NOT NULL DEFAULT '0',
    `app_supplier_id` int(11) NOT NULL,
    `app_warehouse_id` int(11) NOT NULL,
    `notes` varchar(255) DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `fk_app_warehouse_items_userreg` (`userreg`),
    KEY `fk_app_warehouse_items_userupdate` (`userupdate`),
    KEY `fk_app_warehouse_items_warehouse` (`app_warehouse_id`),
    KEY `fk_app_warehouse_items_supplier` (`app_supplier_id`),
    CONSTRAINT `fk_app_warehouse_items_supplier` FOREIGN KEY (`app_supplier_id`) REFERENCES `app_suppliers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_app_warehouse_items_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_app_warehouse_items_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_app_warehouse_items_warehouse` FOREIGN KEY (`app_warehouse_id`) REFERENCES `app_warehouses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
  ) ENGINE = InnoDB DEFAULT CHARSET = latin1;
  
CREATE TABLE IF NOT EXISTS `app_warehouse_movements` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `descri` varchar(255) DEFAULT NULL,
    `quantity` int(11) NOT NULL,
    `address` varchar(255) NOT NULL,
    `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `userreg` int(11) NOT NULL,
    `userupdate` int(11) NOT NULL,
    `app_supplier_id` int(11) NOT NULL,
    `app_warehouse_id` int(11) NOT NULL,
    `notes` varchar(255) DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `fk_app_warehouse_movements_userreg` (`userreg`),
    KEY `fk_app_warehouse_movements_userupdate` (`userupdate`),
    KEY `fk_app_warehouse_movements_warehouse` (`app_warehouse_id`),
    KEY `fk_app_warehouse_movements_supplier` (`app_supplier_id`),
    CONSTRAINT `fk_app_warehouse_movements_supplier` FOREIGN KEY (`app_supplier_id`) REFERENCES `app_suppliers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_app_warehouse_movements_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_app_warehouse_movements_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_app_warehouse_movements_warehouse` FOREIGN KEY (`app_warehouse_id`) REFERENCES `app_warehouses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
  ) ENGINE = InnoDB DEFAULT CHARSET = latin1;