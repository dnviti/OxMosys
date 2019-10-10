SELECT
concat('<span class="nowrap">', al.datare, '</span>') as "Data",
au.username as "Utente",
al.tablename as "Tabella",
al.operation as "Operazione",
al.rawvalues as "JSON"
FROM `app_dml_operation_log` al
JOIN app_users au ON au.id = al.userreg
ORDER BY `datare` DESC
limit 100