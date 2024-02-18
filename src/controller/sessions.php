<?php

    require_once __DIR__ .  '/../../resource/http_response.php';
    require_once __DIR__ .  '/../../resource/mypdo.php';
    require_once __DIR__ .  '/../../resource/client.php';
    require_once __DIR__ .  '/../../resource/crypto.php';
    require_once __DIR__ .  '/../../resource/mydatetime.php';
    
    class SessionController
    {
        public static function render_sessions_page()
        {
            include __DIR__ . '/../view/sessions.php';
        }

        public static function expire_session($id_session)
        {   
            session_start();

            if ((isset($_SESSION['LOGGED']) && isset($_SESSION['ID_USER'])) === false)
            {
                session_destroy();
                http_response::client_error(401);
            }

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
    }

?>