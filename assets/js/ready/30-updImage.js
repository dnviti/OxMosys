$("#uploadImage").change(function (e) {
    $("#f-warehouse-item").submit();
});

$("#f-warehouse-item").on('submit', (function (e) {
    e.preventDefault();
    //var form = document.getElementById("f-warehouse-item");
    var formData = new FormData(this);

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
                // view uploaded file.
                $("#preview img").attr("src", data);
                location.reload();
            }
        },
        error: function (e) {
            $("#err").html(e).fadeIn();
        }
    });
}));