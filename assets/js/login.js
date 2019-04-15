$("#btn-login").click(function (event) {

    event.preventDefault();

    sendAjaxData(
        form_action_url = "php/actions/user_login.php",
        form_action_type = null,
        form_id = "f-login",
        obj_data = null,
        to_page = 1,
        modalResultMessageOK = false,
        modalResultMessageERROR = "Errore: Login Fallito",
        loadingMessage = "Login in corso...",
        loadingColor = "#ccb300",
        loadingText = "black",
        function (res) {
            if ($("#cookiesOnBtn").attr("aria-pressed") == "true") {
                document.cookie = "USERNAME=" + $("#USERNAME").val();
            }
        }
    );

});

$("#cookiesOnBtn").on("click", function (e) {

    var cookies = "false";

    //if ($(this).attr("aria-pressed") == "false") {
    if ($(this).hasClass("btn-danger")) {
        if (confirm("Confermando questa impostazione verranno utilizzati i cookies per memorizzare il nome utente, continuare?")) {
            $(this).html("Ricordami: Sì");
            $(this).removeClass("btn-danger");
            $(this).addClass("btn-success");
            $(this).css("font-weight", "500");
            //$(this).attr("aria-pressed", "true");
            $(this).button("toggle");
            cookies = "true";
        } else {
            $(this).html("Ricordami: No");
            $(this).removeClass("btn-success");
            $(this).addClass("btn-danger");
            $(this).css("font-weight", "");
            //$(this).attr("aria-pressed", "true");
            $(this).button("toggle");
        }
    } else {
        $(this).html("Ricordami: No");
        $(this).removeClass("btn-success");
        $(this).addClass("btn-danger");
        $(this).css("font-weight", "");
        //$(this).attr("aria-pressed", "true");
        $(this).button("toggle");
    }

    $("#cookiesOn").val(cookies);

    createCookie("COOKIES", cookies, 30);
    //document.cookie = "COOKIES=" + cookies;

});

$(document).ready(function () {
    var cookies = "false";

    if (readCookie("COOKIES") == "true") {
        $("#cookiesOnBtn").html("Ricordami: Sì");
        $("#cookiesOnBtn").removeClass("btn-danger");
        $("#cookiesOnBtn").addClass("btn-success");
        $("#cookiesOnBtn").css("font-weight", "500");
        //$("#cookiesOnBtn").attr("aria-pressed", "false");
        cookies = "true";
    } else {
        $(this).html("Ricordami: No");
        $(this).removeClass("btn-success");
        $(this).addClass("btn-danger");
        $(this).css("font-weight", "");
        //$(this).attr("aria-pressed", "false");
    }

    $("#cookiesOn").val(cookies);

    createCookie("COOKIES", cookies, 30);
});

$("#change-pass").click(function () {
    console.log("changing password");
});

// Abilita il click sull'utente per la modifica 