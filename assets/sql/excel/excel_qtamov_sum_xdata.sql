SELECT
a.id AS "ID",
DATE_FORMAT(z.lastupdate, '%d/%m/%Y') AS "Data Mov.",
b.taglia AS "Taglia",
c.buname AS "Fornitore",
b.genere AS "Genere",
b.tipo AS "Tipo",
b.modello AS "Modello",
b.colore AS "Colore",
a.CODE AS "Codice",
a.unitprice AS "Prz Uni",
COALESCE(SUM(z.quantity), 0) AS "Qta Mov.",
ABS(COALESCE(SUM(z.quantity), 0) * a.unitprice) * IF(d.`type` = '-', 1, IF(d.id = 7, 1, -1)) AS "Val Mov."
FROM app_warehouse_movements z
JOIN app_warehouse_causals d ON z.app_warehouse_causals_id = d.id
JOIN app_warehouse_items a ON a.id = z.app_warehouse_items_id
JOIN app_custom_warehouse_items b ON a.id = b.app_warehouse_items_id
JOIN app_suppliers c ON a.app_suppliers_id = c.id
WHERE DATE(z.lastupdate) BETWEEN
	IFNULL(STR_TO_DATE(DATE_FORMAT('2019-09-01', '%Y-%m-%d'), '%Y-%m-%d'), DATE(SYSDATE())) AND
	IFNULL(STR_TO_DATE(DATE_FORMAT('2019-09-30', '%Y-%m-%d'), '%Y-%m-%d'), DATE_SUB(DATE(SYSDATE()),INTERVAL -1 DAY))
GROUP BY a.id
ORDER BY a.id, DATE(z.lastupdate)