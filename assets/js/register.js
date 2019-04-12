$("#btn-register").click(function (event) {

    // controlli sui campi obbligatori

    var err = 0;
    $("input, select, textarea").each(function () {
        // tipo valore nullo
        switch ($(this).attr("aria-nullable")) {
            case "NO":
                if ($(this).val() == "" && $(this).attr("NAME") != "ID") {
                    $(this).css("border", "red solid 2px");
                    console.log($(this).attr("ID") + " --> " + $(this).val());
                    err++;
                } else {
                    $(this).css("border", "");
                }
                break;
            case "YES":
                $(this).val() == "" ? $(this).css("border", "") : null;
                break;
        }
        // controlli sui valori specifici (email, regole password, regole username...)
        // ...
    });

    if (err > 0) {
        bootbox.alert("Informazioni Mancanti!");
    } else {


        event.preventDefault();


        sendAjaxData(
            "php/actions/user_register.php",
            "f-register",
            null,
            6,
            modalResultMessageOK = "Registrazione Utente Completata",
            modalResultMessageERROR = "Errore: Registrazione Utente Fallita",
            loadingMessage = "Registrazione Utente in corso...",
            loadingColor = "#ccb300",
            loadingText = "black"
        );

    }
});

$("#btn-save").click(function (event) {
    event.preventDefault();

    sendAjaxData(
        "php/actions/user_register.php",
        "f-register",
        null,
        6,
        modalResultMessageOK = "Salvataggio Completato",
        modalResultMessageERROR = "Errore: Salvataggio Utente Fallito",
        loadingMessage = "Salvataggio Utente in corso...",
        loadingColor = "#ccb300",
        loadingText = "black"
    );
});

$("#btn-delete").click(function (event) {

    event.preventDefault();

    bootbox.confirm("Eliminare questo utente?", function (result) {

        if (result) {

            sendAjaxData(
                "php/actions/user_delete.php",
                "f-register",
                null,
                6,
                modalResultMessageOK = "Eliminazione Utente Completata",
                modalResultMessageERROR = "Errore: Eliminazione Utente Fallita",
                loadingMessage = "Cancellazione Utente in corso...",
                loadingColor = "#ccb300",
                loadingText = "black"
            );

        }

    });

});

// altro javascript
var userArr = ["", ""];
$("#users_Nome, #users_Cognome").on("keyup", function () {
    if ($(this).attr("ID") == "users_Nome") {
        userArr[0] = $(this).val();
    }
    if ($(this).attr("ID") == "users_Cognome") {
        userArr[1] = $(this).val();
    }

    $("#p_user_name")
        .val(userArr[0].toLowerCase() + "." + userArr[1].toLowerCase());
});

var idUser = $("#p_user_id").val();

$(document).ready(function () {
    getQueryValueAsync(`
        SELECT
            name, 
            surname,
            username
        FROM app_users
        where id=${idUser}`,
        function (json) {
            $("#dettRegUser").empty();
            $("#dettRegUser").append(json2Table(json));
        });
});