if ($(".tbContainer table").length > 0) {
    try {
        // Datatable Initialization 
        var datatable = $(".tbContainer table").DataTable({
            "paging": true,
            "pagingType": "full",
            "pageLength": 50,
            "lengthChange": true,
            "language": {
                "paginate": {
                    "next": "Successiva",
                    "previous": "Precedente",
                    "first": "Prima",
                    "last": "Ultima"
                },
                "search": "Cerca",
                "info": "Pagina _PAGE_ di _PAGES_ (Righe _START_/_END_ di _TOTAL_ righe)",
                "lengthMenu": "Mostra _MENU_ righe"
            },
            responsive: true,

            dom: "<'row'<'col-sm-12'l>>" +
                "<'row'<'col-sm-12'f>>" +
                "<'row'<'col-sm-12'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",

            "initComplete": function (settings, json) {
                setHomepageReportProps();

                // Recover last search from local storage by page
                $("#table_homepage_filter input:first-child").val(localStorage.getItem("table-search-page-" + $("#p_page_id").val()));
                $("#table_homepage_filter input:first-child").focus();
                $("#table_homepage_filter input:first-child").trigger("keyup");

                // Save last search on the table by page
                $("#table_homepage_filter").on("change, keyup", function () {
                    localStorage.setItem("table-search-page-" + $("#p_page_id").val(), $("#table_homepage_filter input:first-child").val());
                });

                HoldOn.close();
            }

        });
        // --------------------- 
        /*
                $('.tbContainer table').on('page.dt', function () {
                    setHomepageReportProps();
                });

                $('.tbContainer table').on('search.dt', function () {
                    setHomepageReportProps();
                });
        */
        $('.tbContainer table').on('draw.dt', function () {
            setHomepageReportProps();
        });
    } catch (error) {
        console.log(error);
        HoldOn.close();
    }

} else {
    HoldOn.close();
}