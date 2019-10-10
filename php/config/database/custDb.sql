USE `oxmosys_db`;
CREATE TABLE IF NOT EXISTS `app_custom_warehouse_items` (
  `app_warehouse_item_id` int(11) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `modello` varchar(255) NOT NULL,
  `colore` varchar(255) NOT NULL,
  `taglia` varchar(255) NOT NULL,
  KEY `fk_app_custom_warehouse_items_userreg` (`app_warehouse_item_id`),
  CONSTRAINT `fk_app_custom_warehouse_items_userreg`
  FOREIGN KEY (`app_warehouse_item_id`)
  REFERENCES `app_warehouse_items` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = latin1;

INSERT INTO `app_warehouses` (`code`, `descri`, `address`, `userreg`, `userupdate`) VALUES ('MAIN', 'Main Warehouse', 'Location', 1, 1);


INSERT INTO `app_warehouse_causals` (`id`, `code`, `descri`, `userreg`, `userupdate`) VALUES (1, 'CARICO_FORNITORE', 'Carico per acquisto merce presso fornitore', 1, 1);
INSERT INTO `app_warehouse_causals` (`id`, `code`, `descri`, `userreg`, `userupdate`) VALUES (2, 'SCARICO_NEGOZIO', 'Scarico per vendita al negozio', 1, 1);
INSERT INTO `app_warehouse_causals` (`id`, `code`, `descri`, `userreg`, `userupdate`) VALUES (3, 'SCARICO_MATTEW', 'Scarico per vendita su facebook', 1, 1);
INSERT INTO `app_warehouse_causals` (`id`, `code`, `descri`, `userreg`, `userupdate`) VALUES (4, 'SCARICO_ECOMMERCE', 'Scarico per vendita su e-commerce', 1, 1);
INSERT INTO `app_warehouse_causals` (`id`, `code`, `descri`, `userreg`, `userupdate`) VALUES (5, 'SCARICO_RESO_FORNITORE', 'Scarico da reso fornitore', 1, 1);
INSERT INTO `app_warehouse_causals` (`id`, `code`, `descri`, `userreg`, `userupdate`) VALUES (6, 'CARICO_RESO_CLIENTE', 'Carico da reso cliente', 1, 1);
INSERT INTO `app_warehouse_causals` (`id`, `code`, `descri`, `userreg`, `userupdate`) VALUES (7, 'CARICO_RETTIFICA_INVENTARIALE', 'Carico rettifica inventario', 1, 1);
INSERT INTO `app_warehouse_causals` (`id`, `code`, `descri`, `userreg`, `userupdate`) VALUES (8, 'SCARICO_RETTIFICA_INVENTARIALE', 'Scarico rettifica inventario', 1, 1);