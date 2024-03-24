<?php

    require_once __DIR__ .  '/../../utils/httpkit/http_response.php';
    require_once __DIR__ .  '/../../utils/mydatetime.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../view/assets/sessions_view.php';
    require_once __DIR__ . '/../model/session.php';
    require_once __DIR__ . '/../model/session_dates.php';
    require_once __DIR__ . '/signin.php';
    
    class SessionController
    {
        public const SESSION_STATUS_CHECK_MS = 4000;

        public static function renderSessionsPage()
        {   
            $navbar = Navbar::getPrivate('sessions');
            
            $sessions = SessionModel::sel_sessions_by_userID_sessionToken_filtered
            (
                session_token: $_SESSION['SESSION_TOKEN'], 
                id_user:       $_SESSION['ID_USER']
            );

            $sessions_view = SessionsView::get($sessions);

            include __DIR__ . '/../view/sessions.php';
        }

        public static function initSession(array $client, int $id_user, string $keepsigned)
        {
            $_SESSION['SIGNED_IN'] = true;

            $session = new SessionModel(ip: $client['ip'], os: $client['os'], browser: $client['browser'], id_user: $id_user);
            $session_dates = new SessionDatesModel(session_token: $session->getSessionToken());
            
            MyPDO::connect($_ENV['SEL_USERNAME'], $_ENV['SEL_PASSWORD'], $_ENV['DB_HOST'], $_ENV['DB_NAME']);
            MyPDO::beginTransaction();
            
            if ($keepsigned === 'on')
            {
                $session_dates->setEnd(MyDatetime::addHours(SessionModel::SESSION_HRS_RANGE_KEEPSIGNED));
                
                setcookie(
                    name:                'session_token', 
                    value:               $session->getSessionToken(), 
                    expires_or_options:  time()+SessionModel::SESSION_HRS_RANGE_KEEPSIGNED, 
                    path:                '/'
                    //secure:            true,
                    //httponly:          true
                );

                $session->setSessionKeySalt(Crypto::genSalt());
                $session_key = Crypto::deriveKey($session->getSessionToken(), $session->getSessionKeySalt());

                $masterkey_encrypted = Crypto::encrypt($_SESSION['MASTER_KEY'], $session_key);

                $us = new UserSecretsModel(
                    masterkey_encrypted: $masterkey_encrypted, 
                    id_user:             $_SESSION['ID_USER']
                );

                if ($us->ins_mKeyEnc_by_userID() === false)
                {
                    MyPDO::rollback();
                    HttpResponse::serverError();
                }
            }
            else
            {
                $session_dates->setEnd(MyDatetime::addHours(SessionModel::SESSION_HRS_RANGE_STD));
            }

            $_SESSION['SESSION_TOKEN'] = $session->getSessionToken();

            if ($session->ins() && $session_dates->ins())
                MyPDO::commit();
            else
            {
                MyPDO::rollBack();
                HttpResponse::serverError();
            }

            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->sel_email_By_userID();

            $_SESSION['USER_DIR'] = $user->getDirName();

            return true;
        }

        public static function expireSession(string $session_token)
        {   
            $session = new SessionModel(session_token: $session_token);

            // $_SESSION['SESSION_TOKEN'] is the current session token
            if ($session->getSessionToken() === $_SESSION['SESSION_TOKEN'])
            {
                SigninController::processSignout();
            }
            else
            {
                $session_expired = $session->expire_by_sessionToken();

                if ($session_expired === true)
                    httpResponse::successful(200);
                else
                    httpResponse::clientError(400, "Invalid session token");
            }
        }
    }

?>