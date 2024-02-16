<?php
    
    define('__ROOT__', '../../../'); 
    define('__QP__', __ROOT__ . 'sql_qrys/');
    
    require_once __ROOT__ . 'model/ds/http_response.php';
    require_once __ROOT__ . 'model/ds/two_factor_auth.php';
    require_once __ROOT__ . 'model/ds/mypdo.php';
    require_once __ROOT__ . 'model/models/user_security.php';
    require_once __ROOT__ . 'model/models/user.php';

    function main()
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'GET': {
                    
                    session_start();

                    check_auth();

                    return trash();

                    break;
                }

                default: {
                    http_response::client_error(405);
                }
            }
        }
        else http_response::server_error(500);
    }


    function check_auth()
    {
        if (!isset($_SESSION['LOGGED']))
        {
            session_destroy();
            header("Location: " . $_ENV['DOMAIN'] . '/view/pages/signin/index.php');
            exit;
        }

        $s = new Session
        (
            id_session: $_SESSION['CURRENT_ID_SESSION'], 
            id_user: $_SESSION['ID_USER']
        );
        
        $session_expired = $s->is_expired_from_idsess();

        if ($session_expired)
        {
            session_destroy();
            header("Location: " . $_ENV['DOMAIN'] . '/view/pages/signin/index.php');
            exit;
        }
    }

    function trash()
    {
        $user = new User(id: $_SESSION['ID_USER']);

        $email = $user->sel_email_from_id();

        $us = new UserSecurity(id_user:$_SESSION['ID_USER']);

        $dkey_salt = $us->sel_dkey_salt_from_id();
        $secret_2fa_c = $us->sel_secret_2fa_c_from_id();
        $rkey_c = $us->sel_rkey_from_id();
        $ckey_c = $us->sel_ckey_from_id();

        return [$email, $secret_2fa_c, $rkey_c, $ckey_c];
    }   


?>