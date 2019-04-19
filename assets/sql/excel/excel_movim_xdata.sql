SELECT
DATE_FORMAT(z.lastupdate, '%d/%m/%Y %k:%i') AS "Data Mov.",
b.taglia AS "Taglia",
c.buname AS "Fornitore",
b.genere AS "Genere",
b.tipo AS "Tipo",
b.modello AS "Modello",
b.colore AS "Colore",
a.CODE AS "Codice",
a.unitprice AS "Prz Uni",
COALESCE(z.quantity, 0) AS "Qta Mov.",
COALESCE(z.quantity, 0) * a.unitprice AS "Val Mov."

FROM app_warehouse_items a
JOIN app_custom_warehouse_items b ON a.id = b.app_warehouse_items_id
JOIN app_suppliers c ON a.app_suppliers_id = c.id
JOIN app_warehouse_movements z ON a.id = z.app_warehouse_items_id
WHERE z.lastupdate BETWEEN
	IFNULL(STR_TO_DATE(DATE_FORMAT(?, '%Y-%m-%d'), '%Y-%m-%d'), DATE_SUB(DATE(SYSDATE()),INTERVAL 1 DAY)) AND
	IFNULL(STR_TO_DATE(DATE_FORMAT(?, '%Y-%m-%d'), '%Y-%m-%d'), DATE_SUB(DATE(SYSDATE()),INTERVAL -1 DAY));