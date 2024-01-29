<?php

    require_once __ROOT__ . 'model/google2FA.php';
    require_once __ROOT__ . 'model/mypdo.php';
    require_once __ROOT__ . 'model/qry.php';
    require_once __ROOT__ . 'model/functions.php';


    function handle_post()
    {
        if (count($_POST) === 1 && key_contains($_POST, 'otp'))
        {
            htmlspecialchars_array($_POST);

            $input_code = $_POST['otp'];

            session_start();

            if (!isset($_SESSION['OTP_CHECKING']) || !isset($_SESSION['AUTH_1FA']) || !isset($_SESSION['ID_USER']))
            {
                http_response::client_error(401);
            }

            $id_user = $_SESSION['ID_USER'];

            $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);

            $email = QRY::sel_email_from_id($conn, $id_user, __QP__);
            $secret_2fa = QRY::sel_secret_2fa_from_id($conn, $id_user, __QP__);

            MYPDO::close_connection($conn);

            $g = new Google2FA($email, $secret_2fa);

            if ($input_code == $g->get_code() || $input_code === $g->get_code())
            {
                $_SESSION['AUTH_2FA'] = true;
                $_SESSION['LOGGED'] = true;

                unset($_SESSION['OTP_CHECKING']);

                http_response::successful(
                    200, 
                    false, 
                    array("redirect" => $_ENV['DOMAIN'] . '/view/private/private.php')
                );
            }
            else
            {
                http_response::client_error(400, "Code is wrong");
            }
        }
        else 
        { 
            http_response::client_error(404); 
        }
    }

?>