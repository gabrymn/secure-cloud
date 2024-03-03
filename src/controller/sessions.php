<?php

    require_once __DIR__ .  '/../../resource/http/httpResponse.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/session.php';
    require_once __DIR__ . '/../model/sessionDates.php';

    class SessionController
    {
        public const SESSION_STATUS_CHECK_MS = 4000;

        public static function renderSessionsPage()
        {
            $navbar = Navbar::getPrivate('sessions');
            include __DIR__ . '/../view/sessions.php';
        }

        public static function initSession($ip, $os, $browser, $id_user)
        {
            $_SESSION['LOGGED'] = true;

            $session = new SessionModel(ip: $ip, id_user: $id_user, os: $os, browser: $browser);
            $session_dates = new SessionDatesModel();

            $id_session = $session->sel_sessionID_by_clientInfo();

            if ($id_session === -1)
            {
                // create session
                $session->setSessionIDRandom();
                $session->setOS(client::getOS());
                $session->setBrowser(client::getBrowser());

                $_SESSION['CURRENT_ID_SESSION'] = $session->getSessionID();

                $session_dates->setSessionID($session->getSessionID());

                $session_dates->setStartDateNow();
                $session_dates->setRecentActivityDateNow();

                self::storeSessionInfo($session, $session_dates);
            }
            else
            {
                // load session
                $_SESSION['CURRENT_ID_SESSION'] = $id_session;

                $session_dates->setSessionID($session->getSessionID());
                $session_dates->setRecentActivityDateNow();
                $session_dates->upd_recentActivity_by_sessionID();
            }

            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->sel_email_By_userID();

            $_SESSION['USER_DIR'] = FileSysHandler::getUserDir($user->getUserID(), $user->getEmail());

            return true;
        }

        public static function expireSession($id_session)
        {   
            $sd = new SessionDatesModel(id_session: $id_session);

            $sd->setEndDateNow();

            $session_expired = $sd->expire_by_sessionID();

            if ($session_expired === false)
                httpResponse::clientError(400, "Invalid session ID");

            // User has sent the id_session, if it equals of the current id session => signout()
            if ($sd->getSessionID() === $_SESSION['CURRENT_ID_SESSION'])
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

        private static function storeSessionInfo(SessionModel $s, SessionDatesModel $sd)
        {
            myPDO::connect('insert');

            try {

                myPDO::beginTransaction();

                $status_qry1 = $s->ins();
                $status_qry2 = $sd->ins();

                if ($status_qry1 && $status_qry2)
                {
                    myPDO::commit();
                    return true;
                }
                
                myPDO::rollBack();

            } catch (Exception $e)
            {
                myPDO::rollBack();
            }

            return false;
        }
    }

?>