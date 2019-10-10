$("#download_json").click(function () {
    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    if (isChrome) {
        var sql = `SELECT r.id, u.id id_user, concat(u.cognome,' ',u.nome) utente, DATE_FORMAT(r.data, "%d/%m/%Y") data_registrazione, a.tipo, a.classe, DATE_FORMAT(truncate(date_add(truncate(r.data, 0),interval a.gg_validita day),0), "%d/%m/%Y") as Scadenza, r.valore, r.note , e.username referente
    FROM registro_incassi r
    join users u on r.id_user=u.id
    join anagrafica_incassi a on r.id_tipo=a.id
    join users e on r.id_userre=e.id
    order by r.data,r.id`;
        getQueryValueAsync(sql, function (json) {
            var dwnCSV = convertToCSV(json); // JSON.stringify(json);
            download_file("lista_ingressi_" + Date.now() + ".csv", dwnCSV);
        });
    } else {
        alert("Download disponibile solo su Chrome");
    }
});