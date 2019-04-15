$(document).ready(function(){

/**
 * Source Folder: "assets/js/ready/" 
 * Source File Name: "0-slidemenu.js"
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
 * Source File Name: "10-menuSearch.js"
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
 * Source File Name: "20-datatable.js"
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
 * Source File Name: "90-homepage.js"
 */

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
});
