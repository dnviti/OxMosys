SELECT
concat(
    '<span><button onclick="',
    concat('window.open(', '''?p=2&ID=', a.id, ''')'),
    '" class="btn btn-link btn-table" data-toggle="tooltip" data-placement="right" title="Modifica">',
    '<i class="fas fa-image"></i>',
    '</button></span>'
) as "<i class='fas fa-image'></i>",
CONCAT(
'<button type="button" class="btn btn-danger" style="width:53px">',
b.taglia,
'</button>'
) as "Taglia",
c.buname AS "Fornitore",
b.genere AS "Genere",
case when a.obsolete = 0 then 'Sì' else 'No' end AS "Con.",
b.tipo AS "Tipo",
b.modello AS "Modello",
b.colore AS "Colore",
a.CODE AS "Codice",
/*b.taglia AS "Taglia",*/
CONCAT('€ ', format(a.unitprice, 2)) AS "Prz Uni",
COALESCE(z.quantity, 0) AS "Tot Giac",
CONCAT('€ ', format(COALESCE(z.quantity, 0) * a.unitprice, 2)) AS "Val Giac"
FROM app_warehouse_items a
JOIN app_custom_warehouse_items b ON a.id = b.app_warehouse_items_id
JOIN app_suppliers c ON a.app_suppliers_id = c.id
LEFT JOIN app_warehouse_movements z ON a.id = z.app_warehouse_items_id
WHERE a.obsolete = 0