$(document).ready(function () {
    $(".tbContainer").DataTable({
        "paging": true,
        "pagingType": "simple",
        "pageLength": 25,
        "lengthChange": true,
        responsive: true
    });
});