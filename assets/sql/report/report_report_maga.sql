select
b.descri as "Tipo",
concat(
        '<button class="btn btn-primary download-excel" ',
        'data-queryfilepath="',
        a.query_path,
        '" ',
        'data-filename="',
        a.outfilename,
        '" ',
        'data-inputs="inidate=date=Data Inizio&findate=date=Data Fine"'
        '>',
        a.descri,
        '</button>'
    ) as "Scarica"
from app_reports a
join app_reports_type b on
    a.app_reports_type_id = b.id
where app_reports_type_id = 0