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