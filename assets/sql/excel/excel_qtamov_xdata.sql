SELECT 
IFNULL(DATE_FORMAT(t.datamov, '%d/%m/%Y'), 'Nessun Movimento') AS "Data Mov.",
t.causale AS "Causale",
t.taglia AS "Taglia",
t.buname AS "Fornitore",
t.genere AS "Genere",
t.tipo AS "Tipo",
t.modello AS "Modello",
t.colore AS "Colore",
t.CODE AS "Codice",
t.unitprice AS "Prz Uni",
COALESCE(SUM(t.quantity), 0) * t.unitprice AS "Val Mov",
COALESCE(SUM(t.quantity), 0) AS "Tot Mov"
FROM (
	SELECT
	z.lastupdate AS datamov,
	d.descri AS causale,
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
	
	FROM app_warehouse_movements z
	JOIN app_warehouse_causals d ON z.app_warehouse_causals_id = d.id
	JOIN app_warehouse_items a ON a.id = z.app_warehouse_items_id
	JOIN app_custom_warehouse_items b ON a.id = b.app_warehouse_items_id
	JOIN app_suppliers c ON a.app_suppliers_id = c.id
	WHERE z.lastupdate BETWEEN
		IFNULL(STR_TO_DATE(DATE_FORMAT(?, '%Y-%m-%d'), '%Y-%m-%d'), DATE_SUB(DATE(SYSDATE()),INTERVAL 0 DAY)) AND
		IFNULL(STR_TO_DATE(DATE_FORMAT(?, '%Y-%m-%d'), '%Y-%m-%d'), DATE_SUB(DATE(SYSDATE()),INTERVAL -1 DAY))
	OR z.lastupdate IS null
	
) AS t
GROUP BY 
t.item_id