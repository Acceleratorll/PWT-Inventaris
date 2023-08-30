function selectInput(element, url, placeholder) {
    $(element).select2({
        ajax: {
            url: url,
            type: "GET",
            dataType: "json",
            delay: 150,
            processResults: function (data) {
                return {
                    results: data,
                };
            },
            error: function (xhr, status, error) {
                console.log(error);
            },
            cache: true,
        },
        placeholder: placeholder,
        minimumInputLength: 1,
        width: "resolve",
    });
}
