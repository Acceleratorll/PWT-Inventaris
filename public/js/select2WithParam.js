function selectInputWithParam(element, url, placeholder, parameter) {
    $(element).select2({
        ajax: {
            url: url,
            type: "GET",
            dataType: "json",
            delay: 150,
            data: function (params) {
                return {
                    term: params.term,
                    data: parameter,
                };
            },
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
        width: "resolve",
    });
}
