$('#otp_form').on('submit', async (e) => {

    e.preventDefault();
    sendOTP();
});

$('#otp').on('input', () => {

    const pattern_6digits = /^\d{6}$/;
    const otp = $('#otp').val();

    if (!(!isNaN(parseInt(otp))))
    {
        $('#otp').val("");
        return;
    }

    if (pattern_6digits.test(otp))
        sendOTP();
})

const sendOTP = async () => {

    var formData = new FormData(document.getElementById('otp_form'));
    
    try {
        const response = await fetch('/auth2', 
        {
            method: 'POST',
            body: formData,
        });

        if (response.ok)
        {
            // test
            //console.log(await response.text());
            //return false;
            
            const json = await response.json();
            window.location.href = json.redirect;
        }
        else
        {   
            const errorTxt = await response.text();

            //console.log(errorTxt);
            //return;

            const errorJson = JSON.parse(errorTxt);
            $('#error_box').css("display", "block");
            $('#error_box').html(errorJson.status_message);
        }

    } catch (error) {
        console.log(error)
        $('#error_box').css("display", "block");
        $('#error_box').html("There was a problem, try again");
    }
}