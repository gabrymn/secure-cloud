<?php

    require_once '../api.php';

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'POST': {

                if (isset($_REQUEST['EMAIL']) && count($_POST) === 1){
                    
                    $tkn = new token(150);
                    $htkn = hash('sha256', $tkn->val());

                    sqlc::connect();
                    $id = sqlc::get_id_user($_REQUEST['EMAIL']);

                    if ($id === 0){
                        response::client_error(400, "Email does not exists");
                    }

                    if (sqlc::rec_account($htkn, $id) === 0){
                        response::server_error(500);
                    }

                    $subject = 'RESET YOUR PASSWORD';
                    $ltkn = $tkn->val();
                    $message = "click https://mywebs.altervista.org/Final/password_reset/{$ltkn} for reset your password";

                    mail($_REQUEST['EMAIL'], $subject, $message);
                    response::add_html(file_get_contents('forms/1.html'));
                    unset($subject);unset($message);unset($htkn);unset($id);

                }else if (isset($_REQUEST['NEW_PASSWORD']) && isset($_REQUEST['EMAIL']) && isset($_REQUEST['HTKN'])){
                    
                    // [change passowrd](1): OK
                    // [turn back] <-
                    // [change password](2): error, !EXPIRED URL!
                    {
                        sqlc::connect();
                        if (sqlc::get_tkn_row($_REQUEST['HTKN']) === 0)
                        {
                            invalid_or_expired_passwordreset_link('password_reset');
                        }
                    }

                    $hpass = password_hash($_REQUEST['NEW_PASSWORD'], PASSWORD_BCRYPT);

                    sqlc::connect();
                    sqlc::del_tkn($_REQUEST['HTKN']);
                    
                    if (sqlc::ch_pass($_REQUEST['EMAIL'], $hpass) === 0){
                        response::server_error(500);
                    }

                    echo "<h1>password changed OK</h2><br>";
                    echo "<h3><span>click <a href ='login'>here</a> for log in</span><h3>";
                    exit;

                }else{
                    response::client_error(400, "Bad parameters");
                }

                break;
            }

            case 'GET': {
                
                if (count($_GET) === 0){
                    response::add_html(file_get_contents('forms/0.html'));
                }

                else if (isset($_REQUEST['TKN'])){

                    $tkn = $_REQUEST['TKN'];

                    // Se length != da 150 inutile controllare nel DB, il token non è valido
                    if (strlen($tkn) !== 150){
                        invalid_or_expired_passwordreset_link('../password_reset');
                    }

                    $htkn = hash("sha256", $tkn);
                    sqlc::connect();
                    $data = sqlc::get_tkn_row($htkn);
                    
                    if ($data === 0){
                        invalid_or_expired_passwordreset_link('../password_reset');
                    }
                    
                    $js = "<script>var input = $('<input>').attr('type', 'hidden').attr('name','EMAIL').val('".$data['email']."');$('#FORM_ID_2').append($(input));var input1 = $('<input>').attr('type', 'hidden').attr('name','HTKN').val('".$htkn."');$('#FORM_ID_2').append($(input1));</script>";
                    
                    response::ctype('HTML');
                    echo "<h2>Change password for [".$data['email'] . "]</h2><br>";
                    response::add_html(file_get_contents('forms/2.html').$js);
                    unset($tkn);unset($htkn);unset($data);unset($js);
                }

                break;
            }
            
            default: {
                response::client_error(405, "Metodo non ammesso");
                break;
            }
        }
    }
    else response::server_error(500);


    function invalid_or_expired_passwordreset_link($redirect_uri){
        response::ctype('HTML');
        response::code(400);
        echo "<h1 style='color:red'>INVALID OR EXPIRED PASSWORD RESET LINK</h1>";
        echo "<span style='font-size:20px;'>CLICK <a href='{$redirect_uri}'>HERE</a> FOR RESET YOUR PASSWORD</span>";
        exit;
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset your password</title>
</head>
<body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>

<script>

    if (window.history.replaceState) window.history.replaceState(null, null, window.location.href);

    /*$(document).ready(() => {

        // Visible FORM ID
        const vFormID = parseInt("<?php //echo $form_id; ?>");

        setFormsVH(
            vFormID, 
            vFormID === 0 ? [1,2] : vFormID === 1 ? [0,2] : [1,0]
        );
    });

    const setFormsVH = (visibleID, hiddenIDs) => {
        hiddenIDs.forEach((hiddenID) => $('#FORM_ID_' + hiddenID).css('display', 'none'));
        $('#FORM_ID_' + visibleID).css('display', 'visible');
    }*/


</script>
</html>