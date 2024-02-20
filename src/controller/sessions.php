<?php

    require_once __DIR__ .  '/../../resource/http_response.php';
    require_once __DIR__ .  '/../../resource/mypdo.php';
    require_once __DIR__ .  '/../../resource/client.php';
    require_once __DIR__ .  '/../../resource/crypto.php';
    require_once __DIR__ .  '/../../resource/mydatetime.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/session.php';

    class SessionController
    {
        public static function render_sessions_page()
        {
            $navbar = Navbar::getPrivate('sessions');
            include __DIR__ . '/../view/sessions.php';
        }

        public static function expire_session($id_session)
        {   
            $id_user = $_SESSION['ID_USER'];

            $s = new Session(id_session:$id_session, id_user:$id_user);
            $s->set_end();

            $session_expired = $s->expire_from_idsess_iduser();

            if ($session_expired === false)
                http_response::client_error(400, "Invalid session ID");

            // User has sent the id_session, if it equals of the current id session => logout()
            if (strcmp($id_session, $_SESSION['CURRENT_ID_SESSION']) === 0)
            {
                session_destroy();
                http_response::successful
                (
                    200, 
                    false, 
                    array("redirect" => '/signin')
                );
            }
            
            http_response::successful(200);
        }

        public static function check_status()
        {
            $session = new Session(id_session: $_SESSION['CURRENT_ID_SESSION'], id_user: $_SESSION['ID_USER']);
            $session_expired = $session->is_expired_from_idsess();
            
            if ($session_expired === 1)
            {
                http_response::client_error(401);
            }
            else
            {
                http_response::successful(200);
            }
        }
    }

?>