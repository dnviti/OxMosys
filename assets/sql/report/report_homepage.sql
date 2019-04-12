SELECT
'',
CONCAT(
'<div class="btn-group mr-2" role="group" aria-label="First group">',
'<button type="button" class="btn btn-primary">U</button>',
'<button type="button" class="btn btn-primary">XS</button>',
'<button type="button" class="btn btn-primary">S</button>',
'<button type="button" class="btn btn-primary">M</button>',
'<button type="button" class="btn btn-primary">L</button>',
'<button type="button" class="btn btn-primary">XL</button>',
'<button type="button" class="btn btn-primary">XXL</button>',
'<button type="button" class="btn btn-primary">XXXL</button>',
'</div>'
) as "Azioni",
c.buname AS "Fornitore",
b.genere AS "Genere",
case when a.obsolete = 0 then 'Sì' else 'No' end AS "Con.",
b.tipo AS "Tipo",
b.modello AS "Modello",
b.colore AS "Colore",
a.CODE AS "Codice",
b.taglia AS "Taglia",
CONCAT('€ ', format(a.unitprice, 2)) AS "Prz Uni",
COALESCE(z.quantity, 0) AS "Tot Giac",
CONCAT('€ ', format(COALESCE(z.quantity, 0) * a.unitprice, 2)) AS "Val Giac"
FROM app_warehouse_items a
JOIN app_custom_warehouse_items b ON a.id = b.app_warehouse_items_id
JOIN app_suppliers c ON a.app_suppliers_id = c.id
LEFT JOIN app_warehouse_movements z ON a.id = z.app_warehouse_items_id
WHERE a.obsolete = 0