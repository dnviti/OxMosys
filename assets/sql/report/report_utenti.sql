select
concat(
    '<span><button onclick="',
    concat('location.href=', '''?p=7&ID=', a.id, ''''),
    '" class="btn btn-link btn-table" data-toggle="tooltip" data-placement="right" title="Modifica">',
    '<i class="fas fa-pencil-ruler"></i>',
    '</button></span>'
) as "",
	concat(a.name, ' ', a.surname) as Utente, 
	a.username as Username,
	r.descri as Ruolo
from app_users a
join app_user_roles r on a.app_user_roles_id = r.id
where a.obsolete = 0
order by a.name, a.surname