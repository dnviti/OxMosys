$("button.ajax-action").click(function (event) {

    var err = 0;

    if ($(this).attr("ajax-action-type") == 'I' || $(this).attr("ajax-action-type") == 'U') {
        // controlli sui campi obbligatori

        $("#" + $(this).attr("ajax-form"))
            .find("input[type!=hidden], select, textarea, div.dropdown.bootstrap-select select")
            .each(function () {
                // tipo valore nullo
                // se il campo è obbligatorio ed è vuoto notifico l'utente dell'errore
                if ($(this).attr("aria-nullable") == "NO" && !$(this).prop("disabled")) {
                    if (($(this).val() == "" || $(this).children(":selected").attr("disabled")) && $(this).attr("NAME") != "ID") {
                        $(this).off("change");
                        if ($(this).parent().hasClass("bootstrap-select")) {
                            $(this).next().addClass("invalid-value");
                            // Quando lo seleziono si toglie il bordo rosso!!!
                            $(this).change(function (e) {
                                $(this).next().removeClass("invalid-value");
                            });
                        } else {
                            $(this).addClass("invalid-value");
                            // Quando lo seleziono si toglie il bordo rosso!!!
                            $(this).change(function (e) {
                                $(this).removeClass("invalid-value");
                            });
                        }
                        console.log($(this).attr("ID"));
                        err++;
                    } else {
                        $(this).removeClass("invalid-value");
                    }
                }
                // controlli sui valori specifici (email, regole password, regole username...)
                // ...
            });
    }

    if (err > 0) {
        bootbox.alert("Informazioni Mancanti");
    } else {
        // Se Elimino chiedo conferma
        if ($(this).attr("ajax-action-type") == 'D') {
            bootbox.confirm("Rendere Obsoleto?", function (result) {
                if (result) {
                    sendAjaxData(
                        $(this).attr("ajax-action"),
                        $(this).attr("ajax-action-type"),
                        $(this).attr("ajax-form"),
                        null,
                        $(this).attr("ajax-topage")
                    );
                }
            });
        } else {
            // se Salvo o Inserisco
            sendAjaxData(
                $(this).attr("ajax-action"),
                $(this).attr("ajax-action-type"),
                $(this).attr("ajax-form"),
                null,
                $(this).attr("ajax-topage")
            );
        }



    }
});