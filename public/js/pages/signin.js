$('#signin_form').on('submit', async (e) => {
    
    e.preventDefault();

    var formData = new FormData(document.getElementById('signin_form'));

    const url = DOMAIN + '/api/signin.php';
    const method = 'POST';
    
    try {   
            const response = await fetch(url, 
            {
                method: method,
                body: formData,
            });

            if (response.ok)
            {
                // debug
                //console.log(await response.text());
                //return false;
                
                const json = await response.json();
                window.location.href = json.redirect;
            }
            else
            {
                //console.log(await response.text());
                //return false;
                
                const errorTxt = await response.text();
                const errorJson = JSON.parse(errorTxt);
                
                if (errorJson.redirect !== undefined)
                    window.location.href = errorJson.redirect;
                else
                {
                    $('#login_error').css("display", "block");
                    $('#login_error').html(errorJson.status_message);
                }
            }

    } catch (error) {
        console.log(error)
        $('#login_error').css("display", "block");
        $('#login_error').html("There was a problem, try again");
    }

    e.preventDefault();
})