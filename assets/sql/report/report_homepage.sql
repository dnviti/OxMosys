SELECT 
t.image AS "<i class='fas fa-image fa-2x'></i>",
t.taglia AS "Taglia",
t.buname AS "Fornitore",
t.genere AS "Genere",
t.tipo AS "Tipo",
t.modello AS "Modello",
t.colore AS "Colore",
t.CODE AS "Codice",
CONCAT('€ ', FORMAT(t.unitprice, 2)) AS "Prz Uni",
CONCAT('€ ', format(COALESCE(SUM(t.quantity), 0) * t.unitprice, 2)) AS "Val Giac",
COALESCE(SUM(t.quantity), 0) AS "Tot Giac"
FROM (
	SELECT
	CONCAT(
	    '<span><button onclick="',
	    concat('window.open(', '''?p=2&ID=', a.id, ''',''Modifica Articolo'')'),
	    '" class="btn btn-link btn-table btn-taglia" data-toggle="popover" data-placement="right" ',
		'data-itemid="',
		a.id,
		'" ',
	    'title="Modifica Articolo">',
	    '<i class="fas fa-image fa-2x"></i>',
	    '</button></span>'
	) as image,
	CONCAT(
	'<button type="button" class="btn btn-danger btn-movimento"',
	    'ajax-action="php/actions/send_data_dml.php" ajax-action-type="I" ajax-data="',
	    'APP_WAREHOUSE_MOVEMENTS-ID=',
	    '&APP_WAREHOUSE_MOVEMENTS-APP_WAREHOUSE_ITEMS_ID=',
	    a.id,
	    '&APP_WAREHOUSE_MOVEMENTS-APP_SUPPLIERS_ID=',
	    c.id,
	    '&APP_WAREHOUSE_MOVEMENTS-APP_WAREHOUSES_ID=',
	    a.app_warehouses_id,
	    '" ',
		'id="btn-movimento-',
		a.id,
		'"',
	' style="width:53px">',
	b.taglia,
	'</button>'
	) as taglia,
	c.buname,
	b.genere,
	b.tipo,
	b.modello,
	b.colore,
	a.CODE,
	a.unitprice,
	z.quantity
	FROM app_warehouse_items a
	JOIN app_custom_warehouse_items b ON a.id = b.app_warehouse_items_id
	JOIN app_suppliers c ON a.app_suppliers_id = c.id
	LEFT JOIN app_warehouse_movements z ON a.id = z.app_warehouse_items_id
	WHERE a.obsolete = 0
) AS t
GROUP BY 
`<i class='fas fa-image fa-2x'></i>`,
`Taglia`,
`Fornitore`,
`Genere`,
`Tipo`,
`Modello`,
`Colore`,
`Codice`,
`Prz Uni`