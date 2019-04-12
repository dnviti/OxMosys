select
    concat(cognome, ' ', nome) as d,
    id as r
from users
where obsoleto = 0
and username != 'admin'
order by cognome, nome;