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