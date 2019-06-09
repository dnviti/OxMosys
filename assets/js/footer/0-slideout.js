/* Slideout Object */
var slideout = new Slideout({
    "panel": document.getElementById("panel"),
    "menu": document.getElementById("menu"),
    "padding": 256,
    "tolerance": 70
});

slideout.on("translatestart", function () {
    $(".toggle-menu").addClass("is-active");
});

slideout.on("beforeclose", function () {
    $(".toggle-menu").removeClass("is-active");
});

document.querySelector(".toggle-menu").addEventListener("click", function () {
    slideout.toggle();
    if (slideout.isOpen()) {
        $(".toggle-menu").addClass("is-active");
    } else {
        $(".toggle-menu").removeClass("is-active");
    }
});

/*slideout.open();*/

/* ------ */