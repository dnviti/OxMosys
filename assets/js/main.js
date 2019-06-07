// Cookies
function createCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }

    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

function getQueryValue(sql) {
    var jsonRes;
    request = $.ajax({
        url: "php/actions/query.php",
        type: "post",
        dataType: "json",
        data: {
            "QUERY": sql
        },
        cache: false,
        async: false,
    });

    request.done(function (response, textStatus, jqXHR) {
        jsonRes = response;
    });

    request.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(errorThrown);
    });

    request.always(function () {

    });

    return jsonRes;
}

function getQueryValueAsync(sql, callback) {
    var jsonRes;

    request = $.ajax({
        url: "php/actions/query.php",
        type: "post",
        dataType: "json",
        data: {
            "QUERY": sql
        },
        cache: false,
        async: true,
    });

    request.done(function (response, textStatus, jqXHR) {
        callback(response);
    });

    request.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(errorThrown);
    });

    request.always(function () {

    });
}

function getQueryFileValueAsync(sqlPath, formSerial, callback) {
    var jsonRes;

    request = $.ajax({
        url: "php/actions/queryFile.php",
        type: "post",
        dataType: "json",
        data: {
            "QUERY": sqlPath,
            "PARAMS": formSerial
        },
        cache: false,
        async: true,
    });

    request.done(function (response, textStatus, jqXHR) {
        callback(response);
    });

    request.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(errorThrown);
    });

    request.always(function () {

    });
}

function json2Lov(lovId, jsonDict, nullDisplay) {
    let dropdown = $("#" + lovId);

    dropdown.empty();

    dropdown.append("<option selected disabled>" + nullDisplay + "</option>");
    dropdown.prop("selectedIndex", 0);

    // Populate dropdown with list of provinces
    $(jsonDict).each(function (i, v) {
        dropdown.append($("<option></option>").attr("value", v["r"]).text(v["d"]));
    });

    return dropdown;
}

function json2Table(tableData) {
    var table = $("<table class=\"table table-striped\"></table>");
    var thead = $("<thead></thead>");
    var hrow = $("<tr></tr>");
    var tbody = $("<tbody></tbody>");
    $(tableData).each(function (i, browData) {
        var brow = $("<tr></tr>");
        if (i == 0) {
            $.each(browData, function (j, cellData) {
                hrow.append($("<th>" + j.charAt(0).toUpperCase() + j.slice(1) + "</th>"));
            });
        }
        $.each(browData, function (j, cellData) {
            brow.append($("<td>" + (cellData == null ? "" : cellData) + "</td>"));
        });
        table.append(thead);
        thead.append(hrow);
        tbody.append(brow);
        table.append(tbody);
    });
    return table;
}

function download_file(name, contents, mime_type) {
    mime_type = mime_type || "text/plain";

    var blob = new Blob([contents], {
        type: mime_type
    });

    var dlink = document.createElement("a");
    dlink.download = name;
    dlink.href = window.URL.createObjectURL(blob);
    dlink.onclick = function (e) {
        // revokeObjectURL needs a delay to work properly
        var that = this;
        setTimeout(function () {
            window.URL.revokeObjectURL(that.href);
        }, 2000);
    };

    dlink.click();
    dlink.remove();
}

function convertToCSV(objArray, delimiter) {
    var delim = delimiter == null ? ";" : delimiter;
    var array = typeof objArray != "object" ? JSON.parse(objArray) : objArray;
    var str = "";
    var headers = "";

    // get headers
    for (var j = 0; j < 1; j++) {
        var keys = "";
        for (var key in array[j]) {
            if (array[j].hasOwnProperty(key)) {
                //key                 = keys,  left of the ":"
                //driversCounter[key] = value, right of the ":"
                keys = keys + key + delim;
            }

        }
        headers = keys.substring(0, keys.length - 1);
    }

    for (var i = 0; i < array.length; i++) {
        var line = "";
        for (var index in array[i]) {
            if (line != "") line += delim;

            line += (array[i][index] != null ? array[i][index] : "");
        }

        str += line + "\r\n";
    }

    return headers + "\r\n" + str;
}

function exportCSVFile(jsonObject, fileTitle) {

    var csv = this.convertToCSV(jsonObject);

    var exportedFilenmae = fileTitle + ".csv" || "export.csv";

    var blob = new Blob([csv], {
        type: "text/csv;charset=utf-8;"
    });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, exportedFilenmae);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", exportedFilenmae);
            link.style.visibility = "hidden";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}

function serializeObj(obj) {
    var str = "";
    for (var key in obj) {
        if (str != "") {
            str += "&";
        }
        str += key + "=" + obj[key];
    }
    return str;
}

function sendAjaxData(
    form_action_url,
    form_action_type = null,
    form_id = null,
    obj_data = null,
    to_page = null,
    modalResultMessageOK = false,
    modalResultMessageERROR = "Errore: Operazione Fallita",
    loadingMessage = "Caricamento in corso...",
    loadingColor = "#ccb300",
    loadingText = "black",
    callback = function () {},
    uppercase = true
) {

    modalResultMessageOK = modalResultMessageOK == undefined ? "Operazione Completata" : modalResultMessageOK;

    var options = {};

    // Starto la request di AJAX
    // Variable to hold request
    var request;

    if (request) {
        request.abort();
    }

    var serializedData;

    if (form_id != null && obj_data == null) {
        var $form = $("#" + form_id);

        serializedData = $form.serialize();
    } else {
        serializedData = serializeObj(obj_data);
    }

    serializedData += (form_action_type != null ? "&OPERATION=" + form_action_type : "");

    serializedData += "&UPPERCASE=" + uppercase;

    options = {
        theme: "sk-cube-grid",
        message: loadingMessage,
        backgroundColor: loadingColor,
        textColor: loadingText
    };

    if (loadingMessage) {
        HoldOn.open(options);
        // console.log(serializedData);
    }

    request = $.ajax({
        url: form_action_url,
        type: "post",
        data: serializedData
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        HoldOn.close();

        showModalAjaxResultDebug(
            response,
            modalResultMessageOK,
            modalResultMessageERROR,
            'Chiudi',
            'Invia e-Mail di assistenza',
            to_page,
            callback
        );
    });

    request.fail(function (jqXHR, textStatus, errorThrown) {
        HoldOn.close();

        showModalAjaxResultDebug(
            errorThrown,
            modalResultMessageOK,
            modalResultMessageERROR,
            'Chiudi',
            'Invia e-Mail di assistenza',
            to_page,
            callback
        );

    });

    request.always(function () {

    });
}

// notifiche
function showModalAjaxResultDebug(response, okmess, title, btncancel, btnsendmail, toPage = null, cback = function () {}, timeout = 500) {
    setTimeout(() => {
        if (response.substring(0, 3) != "ERR" && response.substring(0, 15) != "<!DOCTYPE html>") {
            if (okmess != null && okmess != undefined && okmess) {
                bootbox.alert(okmess, function () {
                    cback(response);
                    if (toPage != false && toPage != "") location.href = "?p=" + toPage;
                });
            } else {
                cback(response);
                if (toPage != false && toPage != "") location.href = "?p=" + toPage;
            }
        } else {
            bootbox.dialog({
                title: title,
                message: ($("#p_is_admin").val() == 1 ? "<span style='font-size:9pt !important' class='error-details-box' rows='10' cols='50'>" + response + "</span>" : " "),
                size: "large",
                onEscape: true,
                backdrop: true,
                buttons: {
                    close: {
                        label: btncancel,
                        className: 'btn-secondary',
                        callback: function () {}
                    },
                    sendMail: {
                        label: btnsendmail,
                        className: 'btn-warning',
                        callback: function () {
                            var params = {
                                DEBUG: 1,
                                SUBJ: title,
                                MESS: escape(response)
                            }

                            sendAjaxData(
                                "php/actions/send_mail.php",
                                null,
                                null,
                                params,
                                null,
                                modalResultMessageOK = "Segnalazione Inviata",
                                modalResultMessageERROR = "Errore durante l'invio",
                                loadingMessage = "Invio mail in corso...",
                            );
                        }
                    }
                }
            });
        }
    }, timeout);
}

function setWebStorage(items = {}) {
    if (typeof (Storage) !== "undefined") {
        for (key in items) {
            localStorage.setItem(key, items[key]);
        }
    } else {
        console.log("Il browser non supporta Web Storage");
    }
}