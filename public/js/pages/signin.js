$('#signin_form').on('submit', async (e) => {
    
    e.preventDefault();
    
    var formData = new FormData(document.getElementById('signin_form'));


    try {   
            const response = await fetch('/signin', 
            {
                method: 'POST',
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
                    $('#error_div').css("display", "block");
                    $('#error_div').html(errorJson.status_message);
                }
            }

    } catch (error) {
        console.log(error)
        $('#error_div').css("display", "block");
        $('#error_div').html("There was a problem, try again");
    }

    e.preventDefault();
})