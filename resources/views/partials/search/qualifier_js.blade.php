<script>
    $(document).ready(function() {
        var resultsTable = $('#results-table').DataTable({
            searchable: true,
            paginate: true,
            // serverSide: true,
        });

        // $('#term').on('keyup', function () {
        //     table.search( this.value ).draw();
        // } );

        $('#search-form').submit(function(e) {
            e.preventDefault();
            var term = $('input[name="term"]').val();

            $.ajax({
                url: $(this).attr('action'),
                method: 'GET',
                data: { term: term },
                success: function(data) {
                    var results = data.results;

                    // Clear existing rows from DataTable
                    resultsTable.clear().draw();

                    // Add rows to DataTable
                    for (var i = 0; i < results.length; i++) {
                        var result = results[data][i];
                        var row = [result.name, result.abbreviation, result.conversion_factor];
                        resultsTable.row.add(row).draw();
                    }
                }
            });
        });
    });
</script>
