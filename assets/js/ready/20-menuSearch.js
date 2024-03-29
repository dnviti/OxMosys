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