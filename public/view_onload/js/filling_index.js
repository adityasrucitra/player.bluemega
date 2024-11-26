(function () {
    table = $('#log_table').DataTable({
        'responsive': true,
        'searching': false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url': document.getElementById('log_table').dataset.sourceUrl,
            'data': function (d) {
                d.equipment = $('#search_equipment').val()
            }
        },
        'columns': [{
                data: 'time_start'
            },
            {
                data: 'time_end'
            },
            {
                data: 'fuel_totalizer_start'
            },
            {
                data: 'fuel_totalizer_end'
            },
            {
                data: 'tank_level_start'
            },
            {
                data: 'tank_level_end'
            },
            {
                data: 'vehicle_name'
            },
            {
                data: 'fuel_filling'
            },
            {
                data: 'fullname'
            },
            {
                data: 'comment'
            },
            {
                data: 'export'
            },
        ],
        order:[[0, 'desc']],
        lengthChange: false,
        autoWidth: false,
        dom: 'Blfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        ]
    });
    table.buttons().container().appendTo('#history_table_wrapper .col-md-6:eq(0)');

    $('#search_equipment').keyup(function () {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function () {
            table.draw();
        }, 1000);
    });

    $('#search_equipment').change(function () {
        table.draw();
    });

    $('input[type=search]').on('search', function () {
        table.draw();
    });

})();