<?php

    require_once __DIR__ .  '/../../resource/http/http_response.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/session.php';

    class SessionController
    {
        public const SESSION_STATUS_CHECK_MS = 4000;

        public static function renderSessionsPage()
        {
            $navbar = Navbar::getPrivate('sessions');
            include __DIR__ . '/../view/sessions.php';
        }

        public static function initSession()
        {
            
        }

        public static function expireSession($id_session)
        {   
            $s = new SessionModel
            (   
                id_session: $id_session, 
                id_user:    $_SESSION['ID_USER']
            );

            $s->setDateEnd();

            $session_expired = $s->expire_by_sessionID_userID();

            if ($session_expired === false)
                httpResponse::clientError(400, "Invalid session ID");

            // User has sent the id_session, if it equals of the current id session => signout()
            if ($id_session === $_SESSION['CURRENT_ID_SESSION'])
            {
                session_destroy();
                httpResponse::successful
                (
                    200, 
                    false, 
                    array("redirect" => '/signin')
                );
            }
            
            httpResponse::successful(200);
        }
    }

?>