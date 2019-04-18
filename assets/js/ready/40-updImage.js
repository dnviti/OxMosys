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