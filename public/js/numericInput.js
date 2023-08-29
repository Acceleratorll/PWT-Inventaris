function numericInput(numericInput) {
    numericInput.addEventListener("input", function (event) {
        const inputValue = event.target.value;
        const numericValue = inputValue.replace(/\D/g, "");

        event.target.value = numericValue;
    });
}