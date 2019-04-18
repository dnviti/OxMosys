$(document).ready(function(){

/**
 * Source Folder: "assets/js/ready/" 
 * Source File Name: "0-browserChecks.js"
 */

if ((is.desktop() && (is.not.chrome()))) {
    options = {
        theme: "custom",
        content: "", //Image
        message: "<i class='fas fa-exclamation-triangle fa-5x'></i><h1>Browser non supportato</h1><br>Molte funzionalit&agrave; non sono disponibili per il browser in uso<br><button type=\"submit\" id=\"btn-close-holdon\" class=\"btn btn-dark\" style=\"margin-top: 10px;font-weight: bolder\" onclick=\"window.open('https://www.google.com/intl/it_ALL/chrome/')\">Scarica Google Chrome</button>",
        backgroundColor: "#c82333",
        textColor: "white"
    };

    HoldOn.open(options);
}
/**
 * Source Folder: "assets/js/ready/" 
 * Source File Name: "10-slidemenu.js"
 */

$("#slnav-logout").click(function (event) {

    event.preventDefault();

    var options = {};

    // Starto la request di AJAX
    // Variable to hold request
    var request;

    if (request) {
        request.abort();
    }

    options = {
        theme: "sk-cube-grid",
        message: "Uscita in corso...",
        backgroundColor: "#ccb300",
        textColor: "black"
    };

    HoldOn.open(options);
    //alert(serializedData);

    request = $.ajax({
        url: "php/actions/logout.php",
        type: "post",
        data: ""
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        //console.log(response);
        //alert(response);
        location.reload();
    });

    request.fail(function (jqXHR, textStatus, errorThrown) {
        HoldOn.close(options);
        alert(textStatus);
        //alert("Errore sconosciuto, riprovare");
        // Per debug
        /*console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );*/
    });

    request.always(function () {

    });

});
/**
 * Source Folder: "assets/js/ready/" 
 * Source File Name: "20-menuSearch.js"
 */

/* Menu Search */
$("input[type=date]").each(function () {
    if ($(this).val().length > 0) {
        $(this).addClass("full");
    } else {
        $(this).removeClass("full");
    }
});

$("input[type=date]").on("change", function () {
    if ($(this).val().length > 0) {
        $(this).addClass("full");
    } else {
        $(this).removeClass("full");
    }
});

// imposto gli le ricerche menu con i valori salvati in precedenza
$("#menu-search").val(localStorage.getItem("menu-search"));
$("span.filterable").filter(function () {
    var value = $("#menu-search").val().toLowerCase();
    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
});
$("input.menu-search-item").each(function () {
    var thisid = $(this).attr("ID");
    $(this).val(localStorage.getItem(thisid));
    var value = $(this).val().toLowerCase();
    $(this).nextAll().filter(function () {

        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});
// filtro i menu in base ai valori preimpostati



$("input.menu-search-item").each(function () {
    $(this).on("keyup", function () {
        var value = $(this).val().toLowerCase();

        setWebStorage({
            [$(this).attr("ID")]: value
        });

        $(this).nextAll().filter(function () {

            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});

$("#menu-search").on("keyup", function () {
    var value = $(this).val().toLowerCase();

    setWebStorage({
        [$(this).attr("ID")]: value
    });

    $("span.filterable").filter(function () {

        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});

$(".collapse").on("shown.bs.collapse hidden.bs.collapse", function () {
    $("#menu-search").off("keyup");
    $("#menu-search").on("keyup", function () {
        var value = $(this).val().toLowerCase();

        setWebStorage({
            [$(this).attr("ID")]: value
        });

        $("span.filterable").filter(function () {

            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});

if ($("#p_page_id").attr("ID") != -1) {
    var pageid = $("#p_page_id").val();
    $("#m-p" + pageid)
        .addClass("active")
        .attr("href", "javascript:void(0)")
        .css("cursor", "default");

    $("#m-p" + pageid).parent().removeClass("collapse");
    $("#m-p" + pageid).parent().prev().removeAttr("data-toggle");
    $("#m-p" + pageid).parent().prev().removeAttr("href");
    $("#m-p" + pageid).parent().prev().removeClass("filterable");
}

/* -------- */
/**
 * Source Folder: "assets/js/ready/" 
 * Source File Name: "30-datatable.js"
 */

/* Datatable Initialization */
$(".tbContainer table").DataTable({
    "paging": true,
    "pagingType": "simple",
    "pageLength": 9999,
    "lengthChange": true,
    responsive: true
});
/* --------------------- */
/**
 * Source Folder: "assets/js/ready/" 
 * Source File Name: "40-updImage.js"
 */

$("#uploadImage").change(function (e) {
    $("#f-warehouse-item").submit();
});

$("#f-warehouse-item").on('submit', (function (e) {
    e.preventDefault();
    //var form = document.getElementById("f-warehouse-item");
    var formData = new FormData(this);

    HoldOn.open({
        theme: "sk-cube-grid",
        message: "Caricamento in corso...",
        backgroundColor: "#ccb300",
        textColor: "black"
    });

    $.ajax({
        url: "php/actions/upload_file.php",
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            $("#err").fadeOut();
        },
        success: function (data) {
            if (data == 'invalid') {} else {
                data = data + "?ts=" + Date.now();
                // view uploaded file.
                $("#preview img").remove();
                $("#preview").append('<img src="' + data + '" />')
                //$("#preview img").attr("src", data);
                HoldOn.close();

                if (window.opener != null) {
                    //var callback = function () {
                    if (is.mobile()) {
                        location.reload();
                        window.opener.location.reload(false);
                    }

                    //}
                    //bootbox.alert("Operazione Completata<br>La pagina precedente verrà aggiornata in automatico", callback)
                    //} else {
                    //    bootbox.alert("Operazione Completata");
                }
            }
        },
        error: function (e) {
            $("#err").html(e).fadeIn();
        }
    });
}));
/**
 * Source Folder: "assets/js/ready/" 
 * Source File Name: "80-excelDownload.js"
 */

if (is.not.chrome()) {
    $(".download-excel").attr("disabled", true);
}

$(".download-excel").click(function () {

    var btn = $(this);

    var mess = " ";

    if (btn.data("inputs")) {

        var inputs = btn.data("inputs").split("&");

        mess += `<form id="report-data">`;
        mess += `<div class="form-group">`;

        var colSet = 2,
            col = colSet;

        inputs.forEach(elem => {
            var elemArray = elem.split("=");

            if (col == colSet) {
                mess += `<div class="row">`;
            }

            mess += `<div class="col">`;
            mess += `
                <label for="${elemArray[0]}">${elemArray[2]}</label>
                <input type="${elemArray[1]}" class="form-control" id="${elemArray[0]}" name="${elemArray[0]}" />
            `;
            mess += `</div>`;

            if (col == 0) {
                mess += `</div>`;
                col = colSet;
            }

            col--;
        });

        mess += `</div>`;
        mess += `</form>`;

    } else {
        mess = "Nessun parametro di input";
    }

    bootbox.dialog({
        title: "Report " + btn.text(),
        message: mess,
        size: 'large',
        onEscape: true,
        backdrop: true,
        buttons: {
            Annulla: {
                label: 'Annulla',
                className: 'btn-secondary',
                callback: function () {

                }
            },
            Scarica: {
                label: 'Scarica',
                className: 'btn-primary',
                callback: function () {
                    HoldOn.open({
                        theme: "sk-cube-grid",
                        message: "Download CSV in corso...",
                        backgroundColor: "#ccb300",
                        textColor: "black"
                    });

                    getQueryFileValueAsync(btn.data("queryfilepath"), $("#report-data").serialize(), function (json) {

                        var dwnCSV = convertToCSV(json); // JSON.stringify(json);
                        download_file(btn.data("filename") + "_" + Date.now() + ".csv", dwnCSV);
                        HoldOn.close();
                    });
                }
            }
        }
    });
});
/**
 * Source Folder: "assets/js/ready/" 
 * Source File Name: "90-homepage.js"
 */

// Setta la data ad ora
// var date = new Date();
// date.setTime(date.getTime() - date.getTimezoneOffset() * 60 * 1000);
// $("#DATETIME-LOCAL-APP_WAREHOUSE_MOVEMENTS-DATEREG").val(date.toISOString().substr(0, 16));


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
});
