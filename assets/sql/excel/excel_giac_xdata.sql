SELECT 
IFNULL(DATE_FORMAT(t.lastupdate, '%d/%m/%Y %k:%i'), 'Nessun Movimento') AS "Data Mov.",
t.taglia AS "Taglia",
t.buname AS "Fornitore",
t.genere AS "Genere",
t.tipo AS "Tipo",
t.modello AS "Modello",
t.colore AS "Colore",
t.CODE AS "Codice",
t.unitprice AS "Prz Uni",
COALESCE(SUM(t.quantity), 0) * t.unitprice AS "Val Giac",
COALESCE(SUM(t.quantity), 0) AS "Tot Giac"
FROM (
	SELECT
	z.lastupdate,
	b.taglia,
	c.buname,
	b.genere,
	b.tipo,
	b.modello,
	b.colore,
	a.CODE,
	a.unitprice,
	z.quantity,
	a.id AS item_id
	FROM app_warehouse_items a
	JOIN app_custom_warehouse_items b ON a.id = b.app_warehouse_items_id
	JOIN app_suppliers c ON a.app_suppliers_id = c.id
	LEFT JOIN app_warehouse_movements z ON a.id = z.app_warehouse_items_id
	WHERE z.lastupdate BETWEEN
	IFNULL(STR_TO_DATE(DATE_FORMAT(?, '%Y-%m-%d'), '%Y-%m-%d'), STR_TO_DATE(DATE_FORMAT('1899-01-01', '%Y-%m-%d'), '%Y-%m-%d')) AND
	IFNULL(STR_TO_DATE(DATE_FORMAT(?, '%Y-%m-%d'), '%Y-%m-%d'), DATE_SUB(DATE(SYSDATE()),INTERVAL -1 DAY))
	OR z.lastupdate IS null
) AS t
GROUP BY 
t.item_id