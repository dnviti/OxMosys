/* Datatable Initialization */
var datatable = $(".tbContainer table").DataTable({
    "paging": true,
    "pagingType": "simple",
    "pageLength": 25,
    "lengthChange": true,
    responsive: true,

    dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",

    "initComplete": function (settings, json) {
        setHomepageReportProps();
        HoldOn.close();
    }

});
/* --------------------- */

$('.tbContainer table').on('page.dt', function () {
    setHomepageReportProps();
});