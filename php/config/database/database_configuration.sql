/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE IF NOT EXISTS `oxmosys` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `oxmosys`;

CREATE TABLE IF NOT EXISTS `app_custom_warehouse_items` (
  `app_warehouse_items_id` int(11) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `modello` varchar(255) DEFAULT NULL,
  `colore` varchar(255) DEFAULT NULL,
  `taglia` varchar(255) NOT NULL,
  `genere` varchar(255) NOT NULL,
  `imagepath` varchar(255) DEFAULT NULL,
  KEY `fk_app_custom_warehouse_items_userreg` (`app_warehouse_items_id`),
  CONSTRAINT `fk_app_custom_warehouse_items_userreg` FOREIGN KEY (`app_warehouse_items_id`) REFERENCES `app_warehouse_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `app_measure_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `descri` varchar(255) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

DELETE FROM `app_measure_units`;
/*!40000 ALTER TABLE `app_measure_units` DISABLE KEYS */;
INSERT INTO `app_measure_units` (`id`, `code`, `descri`, `notes`) VALUES
	(1, 'NUMBER', 'Numero', NULL);
/*!40000 ALTER TABLE `app_measure_units` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `app_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_reports_type_id` int(11) NOT NULL,
  `outfilename` varchar(255) NOT NULL,
  `descri` varchar(255) NOT NULL,
  `query_path` varchar(255) NOT NULL,
  `inputs` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_app_reports_type` (`app_reports_type_id`),
  CONSTRAINT `fk_app_reports_type` FOREIGN KEY (`app_reports_type_id`) REFERENCES `app_reports_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `app_reports_type` (
  `id` int(11) NOT NULL,
  `descri` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `app_suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL DEFAULT '0',
  `buname` varchar(255) NOT NULL,
  `vatid` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `userreg` int(11) NOT NULL,
  `userupdate` int(11) NOT NULL,
  `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `obsolete` tinyint(4) NOT NULL DEFAULT '0',
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_app_suppliers_userreg` (`userreg`),
  KEY `fk_app_suppliers_userupdate` (`userupdate`),
  CONSTRAINT `fk_app_suppliers_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_suppliers_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `app_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `userreg` int(11) NOT NULL,
  `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `obsolete` tinyint(4) NOT NULL DEFAULT '0',
  `app_user_roles_id` int(11) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_app_users_username` (`username`),
  KEY `fk_app_user_roles` (`app_user_roles_id`),
  CONSTRAINT `fk_app_user_roles` FOREIGN KEY (`app_user_roles_id`) REFERENCES `app_user_roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

DELETE FROM `app_users`;
/*!40000 ALTER TABLE `app_users` DISABLE KEYS */;
INSERT INTO `app_users` (`id`, `name`, `surname`, `username`, `password`, `email`, `notes`, `userreg`, `datereg`, `obsolete`, `app_user_roles_id`) VALUES
	(1, 'admin', 'admin', 'admin', '$2y$10$WeX4wUGjx9fMvOnEQQUQne2NuIUfLcq5l9P8/k/7YbzMlB3Y8ATn.', 'admin@localhost', NULL, 1, '2019-06-05 22:34:07', 0, 0);
/*!40000 ALTER TABLE `app_users` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `app_user_roles` (
  `id` int(11) NOT NULL,
  `descri` varchar(255) NOT NULL,
  `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DELETE FROM `app_user_roles`;
/*!40000 ALTER TABLE `app_user_roles` DISABLE KEYS */;
INSERT INTO `app_user_roles` (`id`, `descri`, `datereg`) VALUES
	(0, 'Super Admin', '2019-06-05 22:34:07'),
	(10, 'Titolare', '2019-06-05 22:34:07'),
	(20, 'Gestore', '2019-06-05 22:34:07'),
	(30, 'Dipendente', '2019-06-05 22:34:07'),
	(90, 'Nessuno', '2019-06-07 23:26:50');
/*!40000 ALTER TABLE `app_user_roles` ENABLE KEYS */;

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
  `notes` varchar(255) DEFAULT NULL,
  `obsolete` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_app_warehouses_userreg` (`userreg`),
  KEY `fk_app_warehouses_userupdate` (`userupdate`),
  CONSTRAINT `fk_app_warehouses_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_warehouses_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

DELETE FROM `app_warehouses`;
/*!40000 ALTER TABLE `app_warehouses` DISABLE KEYS */;
INSERT INTO `app_warehouses` (`id`, `code`, `descri`, `country`, `address`, `datereg`, `lastupdate`, `userreg`, `userupdate`, `notes`, `obsolete`) VALUES
	(1, 'MAIN', 'Magazzino Principale', 'IT', 'Location', '2019-06-05 22:34:07', '2019-06-05 22:34:07', 1, 1, NULL, 0);
/*!40000 ALTER TABLE `app_warehouses` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `app_warehouse_causals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `type` char(1) NOT NULL,
  `descri` varchar(255) NOT NULL,
  `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userreg` int(11) NOT NULL,
  `userupdate` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_app_warehouse_causals_userreg` (`userreg`),
  KEY `fk_app_warehouse_causals_userupdate` (`userupdate`),
  CONSTRAINT `fk_app_warehouse_causals_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_warehouse_causals_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

DELETE FROM `app_warehouse_causals`;
/*!40000 ALTER TABLE `app_warehouse_causals` DISABLE KEYS */;
INSERT INTO `app_warehouse_causals` (`id`, `code`, `type`, `descri`, `datereg`, `lastupdate`, `userreg`, `userupdate`, `notes`) VALUES
	(1, 'CARICO_FORNITORE', '+', 'Carico per acquisto merce presso fornitore', '2019-06-05 22:34:07', '2019-06-05 22:34:07', 1, 1, NULL),
	(2, 'SCARICO_NEGOZIO', '-', 'Scarico per vendita al negozio', '2019-06-05 22:34:07', '2019-06-05 22:34:07', 1, 1, NULL),
	(3, 'SCARICO_MATTEW', '-', 'Scarico per vendita su facebook', '2019-06-05 22:34:07', '2019-06-05 22:34:07', 1, 1, NULL),
	(4, 'SCARICO_ECOMMERCE', '-', 'Scarico per vendita su e-commerce', '2019-06-05 22:34:07', '2019-06-05 22:34:07', 1, 1, NULL),
	(5, 'SCARICO_RESO_FORNITORE', '-', 'Scarico da reso fornitore', '2019-06-05 22:34:07', '2019-06-05 22:34:07', 1, 1, NULL),
	(6, 'CARICO_RESO_CLIENTE', '+', 'Carico da reso cliente', '2019-06-05 22:34:07', '2019-06-05 22:34:07', 1, 1, NULL),
	(7, 'CARICO_RETTIFICA_INVENTARIALE', '+', 'Carico rettifica inventario', '2019-06-05 22:34:07', '2019-06-05 22:34:07', 1, 1, NULL),
	(8, 'SCARICO_RETTIFICA_INVENTARIALE', '-', 'Scarico rettifica inventario', '2019-06-05 22:34:07', '2019-06-05 22:34:07', 1, 1, NULL);
/*!40000 ALTER TABLE `app_warehouse_causals` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `app_warehouse_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `descri` varchar(255) DEFAULT NULL,
  `unitprice` double NOT NULL,
  `app_measure_units_id` int(11) NOT NULL DEFAULT '1',
  `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userreg` int(11) NOT NULL,
  `userupdate` int(11) NOT NULL,
  `obsolete` tinyint(4) NOT NULL DEFAULT '0',
  `app_suppliers_id` int(11) NOT NULL,
  `app_warehouses_id` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_app_warehouse_items_userreg` (`userreg`),
  KEY `fk_app_warehouse_items_userupdate` (`userupdate`),
  KEY `fk_app_warehouse_items_warehouse` (`app_warehouses_id`),
  KEY `fk_app_warehouse_items_supplier` (`app_suppliers_id`),
  CONSTRAINT `fk_app_warehouse_items_supplier` FOREIGN KEY (`app_suppliers_id`) REFERENCES `app_suppliers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_warehouse_items_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_warehouse_items_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_warehouse_items_warehouse` FOREIGN KEY (`app_warehouses_id`) REFERENCES `app_warehouses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=561 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `app_warehouse_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_warehouse_items_id` int(11) NOT NULL,
  `descri` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `datereg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userreg` int(11) NOT NULL,
  `userupdate` int(11) NOT NULL,
  `app_warehouse_causals_id` int(11) NOT NULL,
  `app_warehouses_id` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_app_warehouse_movements_warehouse_causal` (`app_warehouse_causals_id`),
  KEY `fk_app_warehouse_movements_userreg` (`userreg`),
  KEY `fk_app_warehouse_movements_userupdate` (`userupdate`),
  KEY `fk_app_warehouse_movements_warehouse` (`app_warehouses_id`),
  KEY `fk_app_warehouse_movements_item` (`app_warehouse_items_id`),
  CONSTRAINT `fk_app_warehouse_movements_item` FOREIGN KEY (`app_warehouse_items_id`) REFERENCES `app_warehouse_items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_warehouse_movements_userreg` FOREIGN KEY (`userreg`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_warehouse_movements_userupdate` FOREIGN KEY (`userupdate`) REFERENCES `app_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_warehouse_movements_warehouse` FOREIGN KEY (`app_warehouses_id`) REFERENCES `app_warehouses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_app_warehouse_movements_warehouse_causal` FOREIGN KEY (`app_warehouse_causals_id`) REFERENCES `app_warehouse_causals` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=561 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
