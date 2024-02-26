<?php

    require_once __DIR__ .  '/../../resource/http/httpResponse.php';
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

        public static function initSession($ip_client, $id_user)
        {
            $_SESSION['LOGGED'] = true;

            $session = new SessionModel(ip: $ip_client, id_user: $id_user);

            $id_session = $session->sel_sessionID_by_userID_clientIP();

            if ($id_session === -1)
            {
                // create session
                $session->setSessionIDRandom();
                $session->setOS(client::getOS());
                $session->setBrowser(client::getBrowser());

                $_SESSION['CURRENT_ID_SESSION'] = $session->getSessionID();

                $session->setDateStart();
                $session->setRecentActivity();

                $session->ins();
            }
            else
            {
                // load session
                $_SESSION['CURRENT_ID_SESSION'] = $id_session;

                $session->setSessionID($id_session);
                $session->setRecentActivity();
                
                $session->upd_recentActivity_by_sessionID();
            }

            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->selEmailFromID();

            $_SESSION['USER_DIR'] = FileSysHandler::getUserDir($user->getUserID(), $user->getEmail());

            return true;
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