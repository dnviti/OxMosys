if ($(".tbContainer table").length > 0) {
    try {
        // Datatable Initialization 
        var datatable = $(".tbContainer table").DataTable({
            "paging": true,
            "pagingType": "full_numbers",
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

            dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",

            "initComplete": function (settings, json) {
                setHomepageReportProps();
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