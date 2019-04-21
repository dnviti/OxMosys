// Setta la data ad ora
// var date = new Date();
// date.setTime(date.getTime() - date.getTimezoneOffset() * 60 * 1000);
// $("#DATETIME-LOCAL-APP_WAREHOUSE_MOVEMENTS-DATEREG").val(date.toISOString().substr(0, 16));

function setHomepageReportProps() {
    $(".btn-movimento").off("click");


    $(".btn-movimento").click(function (e) {

        $("tr").css("background", "");
        var elem = $(this);
        var oldHtmlBtn = $(this).html();
        $(this).attr("disabled", true);
        $(this).html('<i class="fas fa-circle-notch fa-spin fa-2x"></i>');

        // Catturo i dati del movimento dai campi sopra la tabella
        var causale = $("#LOV-APP_WAREHOUSE_CAUSALS-ID").children(":selected");
        var quantita = $("#NUMBER-APP_WAREHOUSE_MOVEMENTS-QUANTITY").val();
        var data = $("#DATETIME_LOCAL-APP_WAREHOUSE_MOVEMENTS-DATEREG").val();

        // Catturo i dati dal bottone (dati di riga)
        /** */

        var ajaxData = $(this).attr("ajax-data").split("&");
        var ajaxDataObj = {};

        if (parseInt(quantita) > 0) {

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

                if (data) {
                    var dateObj = new Date(data);
                    dateObj.setTime(dateObj.getTime() - dateObj.getTimezoneOffset() * 60 * 1000);
                    ajaxDataObj["APP_WAREHOUSE_MOVEMENTS-LASTUPDATE"] = dateObj.toISOString().slice(0, 19).replace('T', ' ');
                }

                var idArticolo = ajaxDataObj["APP_WAREHOUSE_MOVEMENTS-APP_WAREHOUSE_ITEMS_ID"];

                var returnQuery = `SELECT COALESCE(SUM(quantity), 0) AS "Tot Giac"
                                FROM app_warehouse_movements a
                                WHERE a.app_warehouse_items_id = ${idArticolo}`;

                // aggiorno la giacenza totale e il valore di giacenza
                getQueryValueAsync(returnQuery, function (res) {
                    // giacenza totale prima del movimento


                    var newQta = parseInt(res[0]["Tot Giac"]) + parseInt(ajaxDataObj["APP_WAREHOUSE_MOVEMENTS-QUANTITY"]);

                    if (newQta >= 0) {

                        // Invio la chiamata solo se la giacenza non risulta negativa
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
                                        title: '<h5>' + (parseInt(ajaxDataObj["APP_WAREHOUSE_MOVEMENTS-QUANTITY"]) > 0 ? 'Carico Effettuato' : 'Scarico Effettuato') + '</h5>',
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
                    } else {
                        /** Notifico l'utente dell'errore */

                        $.notify({
                            // options
                            title: '<h5>' + (parseInt(ajaxDataObj["APP_WAREHOUSE_MOVEMENTS-QUANTITY"]) > 0 ? 'Carico Errato' : 'Scarico Errato') + '</h5>',
                            message: causale.text() + '<br>Quantit&agrave; Negativa (' + newQta + ')' + ' <span>&#8594;</span> ' + '<a id="link-err-' + idArticolo + '" href="javascript:void(0)">Visualizza Articolo</a>'
                        }, {
                            // settings
                            type: 'danger',
                            newest_on_top: true,
                            delay: 999999,
                            timer: 200,
                            animate: {
                                enter: 'animated shake',
                                exit: 'animated fadeOutRight'
                            },
                            onShow: function (idArt = idArticolo) {
                                $("#link-err-" + idArt).click(function () {
                                    $("tr").css("background", "");
                                    $("#btn-movimento-" + idArt).parent().parent().css("background", "#f2ff88");
                                    $([document.documentElement, document.body]).animate({
                                        scrollTop: $("#btn-movimento-" + idArt).offset().top
                                    }, {
                                        duration: 500,
                                        complete: function () {
                                            for (i = 0; i < 1; i++) {
                                                $("#btn-movimento-" + idArt).parent().parent().fadeTo('fast', 0.5).fadeTo('fast', 1.0);
                                            }
                                        }
                                    });
                                });
                            }
                        });
                        // riabilito il bottone e finisco il caricamento
                        elem.attr("disabled", false);
                        elem.html(oldHtmlBtn);
                    }
                });


            });

        } else {
            /** Notifico l'utente dell'errore */

            $.notify({
                // options
                title: '<h5>Dati Mancanti</h5>',
                message: causale.text() + '<br>Specificare la quantit&agrave;'
            }, {
                // settings
                type: 'warning',
                newest_on_top: true,
                delay: 999999,
                timer: 200,
                animate: {
                    enter: 'animated shake',
                    exit: 'animated fadeOutRight'
                }
            });
            // riabilito il bottone e finisco il caricamento
            elem.attr("disabled", false);
            elem.html(oldHtmlBtn);
        }
    });


    $('.btn-taglia').popover({
        html: true,
        trigger: 'hover',
        placement: 'right',
        title: function () {
            return '-';
        },
        content: function () {
            var itemid = $(this).data('itemid');
            var imageQuery = `
                SELECT imagepath
                FROM app_custom_warehouse_items
                WHERE app_warehouse_items_id = ${itemid}
            `;

            getQueryValueAsync(imageQuery, function (res) {
                var imgsrc = res[0]['imagepath'] + "?ts=" + Date.now();
                $(`#imagepath-${itemid} svg`).remove();
                if (res[0]['imagepath']) {
                    $(`#imagepath-${itemid} img`).remove();
                    $(`#imagepath-${itemid}`).append(`<img src="${imgsrc}" />`);
                } else {
                    $(`#imagepath-${itemid} img`).remove();
                    $(`#imagepath-${itemid}`).text("Nessuna Immagine");
                }
            });

            return `<span id="imagepath-${itemid}"><i class="fas fa-circle-notch fa-spin"></i></span>`;
        }
    });
}