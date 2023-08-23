<script>
    $(document).ready(function() {
        var resultsTable = $('#results-table').DataTable({ // Initialize DataTable
            info: false, // Disable info
        });

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
                        var result = results[i];
                        var row = [result.name, result.material_code, result.desc];
                        resultsTable.row.add(row).draw();
                    }
                }
            });
        });
    });
</script>
