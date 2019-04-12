select
concat(
    '<span><button onclick="',
    concat('location.href=', '''?p=4&ID=', id, ''''),
    '" class="btn btn-link btn-table" data-toggle="tooltip" data-placement="right" title="Modifica">',
    '<i class="fas fa-pencil-ruler"></i>',
    '</button></span>'
) as "",
buname as "Rag. Soc.",
vatid as "P.IVA",
telephone as "Tel.",
address as "Ind.",
email as "e-Mail"
from app_suppliers
where obsolete = 0