<?php

    require_once __DIR__ .  '/../../resource/http_response.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/session.php';

    class SessionController
    {
        public const SESSION_STATUS_CHECK_MS = 4000;

        public static function render_sessions_page()
        {
            $navbar = Navbar::getPrivate('sessions');
            include __DIR__ . '/../view/sessions.php';
        }

        public static function expire_session($id_session)
        {   
            $s = new Session
            (   
                id_session: $id_session, 
                id_user:    $_SESSION['ID_USER']
            );

            $s->set_end();

            $session_expired = $s->expire_from_idsess_iduser();

            if ($session_expired === false)
                http_response::client_error(400, "Invalid session ID");

            // User has sent the id_session, if it equals of the current id session => signout()
            if ($id_session === $_SESSION['CURRENT_ID_SESSION'])
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