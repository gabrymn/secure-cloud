function capitalizeFirstLetter(id) {
    let inputElement = document.getElementById(id);
    let inputValue = inputElement.value;

    let formattedValue = inputValue.replace(/\b\w/g, function (match) {
        return match.toUpperCase();
    });

    inputElement.value = formattedValue;
}

$('#ID_REG_FORM').on('submit', () => {

    if ($('#pwd1').val() !== $('#pwd2').val())
    {
        $('#ERROR_PDM').css("display", "block")
        return false
    }
    else
    {
        
    }
})