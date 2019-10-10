SELECT
concat(
    '<span><button onclick="',
    concat('location.href=', '''?p=2&ID=', a.id, ''''),
    '" class="btn btn-link btn-table" data-toggle="tooltip" data-placement="right" title="Modifica">',
    '<i class="fas fa-pencil-ruler fa-2x"></i>',
    '</button></span>'
) as "",
c.buname AS "Fornitore",
b.genere AS "Genere",
b.tipo AS "Tipo",
b.modello AS "Modello",
b.colore AS "Colore",
a.code as "Codice",
a.descri AS "Descri.",
b.taglia AS "Taglia",
CONCAT('€ ', format(a.unitprice, 2)) AS "Prz. Unitario"
FROM app_warehouse_items a
JOIN app_custom_warehouse_items b ON a.id = b.app_warehouse_items_id
JOIN app_suppliers c ON a.app_suppliers_id = c.id
JOIN app_warehouses d ON a.app_warehouses_id = d.id
WHERE a.obsolete = 1