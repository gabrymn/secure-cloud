$('#signup_form').on('submit', async (e) => {
    // blocca la richiesta HTTP della form
    e.preventDefault();

    if (validateInputs())
    {
        var formData = new FormData(document.getElementById('signup_form'));
        
        formData.delete('pwd_confirm');

        const url = DOMAIN + '/signup';
        const method = 'POST';
        
        try {
            const response = await fetch(url, 
            {
                method: method,
                body: formData,
            });

            if (response.ok)
            {
                // test
                console.log(await response.text());
                return false;
                
                const json = await response.json();
                window.location.href = json.redirect;
            }
            else
            {
                console.log(await response.text());
                return false;
                
                const errorTxt = await response.text();
                const errorJson = JSON.parse(errorTxt);
                $('#input_error').css("display", "block");
                $('#input_error').html(errorJson.status_message);
            }

        } catch (error) {
            console.log(error)
            $('#input_error').css("display", "block");
            $('#input_error').html("There was a problem, try again");
        }
    }

    e.preventDefault();
});

const validateInputs = () => {
    if ($('#id_name').val().length < 2 || $('#id_surname').val().length < 2)
    {
        $('#input_error').css("display", "block");
        $('#input_error').html("Name and surname must have at least 2 characters");
        return false;
    }
    if ($('#pwd').val() !== $('#pwd_confirm').val())
    {
        $('#input_error').css("display", "block");
        $('#input_error').html("Passwords don't match");
        return false;
    }
    if ($('#pwd').val().length < 2)
    {
        $('#input_error').css("display", "block");
        $('#input_error').html("Password must have at least 8 characters");
        return false;
    }
    
    return true;
}

const capitalizeFirstLetter = (id) => {
    let inputElement = document.getElementById(id);
    let inputValue = inputElement.value;

    let formattedValue = inputValue.replace(/\b\w/g, function (match) {
        return match.toUpperCase();
    });

    inputElement.value = formattedValue;
}