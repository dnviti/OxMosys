$(".btn-movimento").click(function (e) {
    var elem = $(this);
    var oldHtmlBtn = $(this).html();
    $(this).attr("disabled", true);
    $(this).html('<i class="fas fa-circle-notch fa-spin fa-2x"></i>');

    // Catturo i dati del movimento dai campi sopra la tabella
    var causale = $("#LOV-APP_WAREHOUSE_CAUSALS-ID").children(":selected");
    var quantita = $("#NUMBER-APP_WAREHOUSE_MOVEMENTS-QUANTITY").val();

    // Catturo i dati dal bottone (dati di riga)
    /** */

    var ajaxData = $(this).attr("ajax-data").split("&");
    var ajaxDataObj = {};

    getQueryValueAsync("SELECT TYPE FROM APP_WAREHOUSE_CAUSALS WHERE ID = " + causale.val(), function (res) {
        // Invio il movimento AJAX
        var type = res[0]["TYPE"];
        var signMult = -1;

        if (type == '+') {
            signMult = 1;
        }

        ajaxData.forEach(function (value, idx) {
            ajaxDataObj[value.split("=")[0]] = value.split("=")[1]
        });

        ajaxDataObj["APP_WAREHOUSE_MOVEMENTS-APP_WAREHOUSE_CAUSALS_ID"] = causale.val();
        ajaxDataObj["APP_WAREHOUSE_MOVEMENTS-QUANTITY"] = quantita * signMult;
        ajaxDataObj["APP_WAREHOUSE_MOVEMENTS-USERREG"] = $("#p_user_id").val();
        ajaxDataObj["APP_WAREHOUSE_MOVEMENTS-USERUPDATE"] = $("#p_user_id").val();

        sendAjaxData(
            elem.attr("ajax-action"),
            elem.attr("ajax-action-type"),
            false,
            ajaxDataObj,
            false,
            modalResultMessageOK = false,
            modalResultMessageERROR = "Errore: Operazione Fallita",
            loadingMessage = false,
            loadingColor = null,
            loadingText = null,
            function (response) {

                var returnQuery = `SELECT 
                CONCAT('€ ', format(COALESCE(SUM(quantity), 0) * unitprice, 2)) AS "Val Giac",
                COALESCE(SUM(quantity), 0) AS "Tot Giac"
                FROM APP_WAREHOUSE_MOVEMENTS A
                JOIN APP_WAREHOUSE_ITEMS B ON A.APP_WAREHOUSE_ITEMS_ID = B.ID
                where APP_WAREHOUSE_ITEMS_ID = (SELECT APP_WAREHOUSE_ITEMS_ID FROM APP_WAREHOUSE_MOVEMENTS WHERE id = ${response}) 
                GROUP BY APP_WAREHOUSE_ITEMS_ID`;

                // aggiorno la giacenza totale e il valore di giacenza
                getQueryValueAsync(returnQuery, function (res) {

                    elem.parent().parent().find("td[headers='Tot Giac']").text(res[0]["Tot Giac"]);
                    elem.parent().parent().find("td[headers='Val Giac']").text(res[0]["Val Giac"]);

                    /** Notifico l'utente dell'avvenuto inserimento */
                    $.notify({
                        // options
                        title: '<h5>Movimento Salvato</h5>',
                        message: causale.text()
                    }, {
                        // settings
                        type: 'success',
                        newest_on_top: true,
                        delay: 5000,
                        timer: 200,
                        animate: {
                            enter: 'animated jackInTheBox',
                            exit: 'animated fadeOutRight'
                        },
                    });
                    // riabilito il bottone e finisco il caricamento
                    elem.attr("disabled", false);
                    elem.html(oldHtmlBtn);
                });
            }
        );
    });

});



$('.btn-taglia').popover({
    html: true,
    trigger: 'hover',
    placement: 'right',
    title: function() {
        return '-';
    },
    content: function () {
        return '<img src="' + $(this).data('imagepath') + '" />';
    }
})