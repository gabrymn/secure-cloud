function redirectSignin()
{
    const url = DOMAIN + '/view/pages/signin/index.php';
    window.location.href = url;
}

function validateEmail() {

    var email = $('#id_email').val();
    
    if (isValidEmail(email)) {
        $('#email_box').css('display', 'none');
        $('#recoverykey_box').css('display', 'block');
    } else {
        alert('Inserisci un indirizzo email valido.');
    }
}

function validateRecoverykey() {

    var recoverykey = $('#id_recoverykey').val();

    if (recoverykey.length > 0)
        send_email_rkey();
    else
        alert('Inserisci una chiave di recupero valida');

}

function validatePassword() {

    var pwd1 = $('#id_pwd1').val();
    var pwd2 = $('#id_pwd2').val();

    if (pwd1 === pwd2)
    {
        send_pwd();
    }
    else
    {
        alert("Le password non corrispondono")
    }

}

function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showEmailForm() {
    // Resetta il form
    $('#recoveryForm')[0].reset();
    // Nascondi il form di recupero
    $('#recoveryForm').hide();
    // Mostra il form dell'email
    $('#emailForm').show();
}

function send_pwd()
{
    var pwd = $('#id_pwd1').val();

    const URL = DOMAIN + '/api/recovery.php';
    
    $.ajax({    

        method: 'POST',
        url: URL,
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
            
            $('#input_error').css("display", "block");
            $('#input_error').html(errorMsg);
        }
    });
}

function send_email_rkey() 
{
    var email = $('#id_email').val();
    var recoverykey = $('#id_recoverykey').val();

    const URL = DOMAIN + '/api/recovery.php';

    $.ajax({

        method: 'POST',
        url: URL,
        dataType: 'json',
        data: {
            email: email,
            recovery_key: recoverykey
        },
        success: function(response) {

            $('#recoverykey_box').css("display", "none");
            $('#password_box').css("display", "block");
        },

        error: function(xhr, status, error) {

            console.log(xhr);
            return;

            errorMsg = ""

            try {
                errorMsg = xhr.responseJSON.status_message
            }
            catch (e)
            {
                errorMsg = "There was a problem, try again";
            }   
            
            $('#input_error').css("display", "block");
            $('#input_error').html(errorMsg);
        }
    });
}