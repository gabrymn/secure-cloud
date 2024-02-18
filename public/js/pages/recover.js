$('#email_form').on('submit', (e) => {

    e.preventDefault();
    $('#email_box').css('display', 'none');
    $('#recoverykey_box').css('display', 'block');
    
});

$('#recoverykey_form').on('submit', (e) => {

    e.preventDefault();
    validateRecoverykey(); 
    
});

function validateRecoverykey() 
{
    var recoverykey = $('#recoverykey').val();

    if (recoverykey.length > 0)
        send_email_rkey();
    else
    {
        $('#error_div').css('display', 'block');
        $('#error_div').html('Inserisci una chiave di recupero valida');
        $('#recoverykey').val("");
    }
}

function send_email_rkey() 
{
    var email = $('#email').val();
    var recoverykey = $('#recoverykey').val();

    $.ajax({

        method: 'POST',
        url: '/recover',
        dataType: 'json',

        data: {
            email: email,
            recoverykey: recoverykey
        },

        success: function(response) {

            $('#recoverykey_box').css("display", "none");
            $('#password_reset_box').css("display", "block");
        },

        error: function(xhr, status, error) {

            errorMsg = ""

            try {
                errorMsg = xhr.responseJSON.status_message
            }
            catch (e)
            {
                errorMsg = "There was a problem, try again";
            }   
            
            $('#error_div').css("display", "block");
            $('#error_div').html(errorMsg);

            $('#recoverykey').val("");
        }
    });
}


$('#password_reset_form').on('submit', (e) => {

    e.preventDefault();

    var pwd1 = $('#id_pwd1').val();
    var pwd2 = $('#id_pwd2').val();

    if (pwd1 === pwd2)
    {
        send_pwd();
    }
    else
    {
        $('#error_div').css("display", "block");
        $('#error_div').html("Password does not match");
    }
});

function send_pwd()
{
    var pwd = $('#pwd').val();

    $.ajax({    

        method: 'POST',
        url: '/recover',
        dataType: 'json',
        data: {
            pwd: pwd,
        },

        success: function(response) {
            $('#password_box').css("display", "none");
            $('#success_box').css("display", "block");
        },

        error: function(xhr, status, error) {

            console.log(xhr.responseText)
            errorMsg = ""

            try {
                errorMsg = xhr.responseJSON.status_message
            }
            catch (e)
            {
                errorMsg = "There was a problem, try again";
            }   
            
            $('#error_div').css("display", "block");
            $('#error_div').html(errorMsg);
        }
    });
}