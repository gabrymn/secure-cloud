<?php

    require_once __DIR__ . '/../../resource/http/http_response.php';
    require_once __DIR__ . '/../../resource/storage/file_sys_handler.php';
    require_once __DIR__ . '/../model/session.php';
    
    class AuthController
    {
        private const REDIRECT_PAGE = '/signin';

        private static function handleResponse(bool $redirect)
        {
            session_destroy();

            if ($redirect)
                httpResponse::redirect(self::REDIRECT_PAGE);
            else
                httpResponse::clientError(401);
        }

        public static function check(bool $redirect, ...$session_args_required)
        {
            if (session_status() === PHP_SESSION_NONE) 
                session_start();

            foreach ($session_args_required as $session_arg) 
            {
                if (is_array($session_arg)) 
                {
                    foreach ($session_arg as $key => $value) 
                    {
                        if (is_array($value))
                        {
                            $almost_one_true = false;
                            $i = 0;

                            do 
                            {   
                                if (isset($_SESSION[$key]) && $_SESSION[$key] === $value[$i])
                                    $almost_one_true = true;
                                
                                $i++;

                            } while ($almost_one_true === false && $i < count($value));

                            if ($almost_one_true === false)
                                self::handleResponse($redirect);
                        }
                        else
                        {
                            if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $value) 
                            {
                                self::handleResponse($redirect);
                            }
                        }
                    }
                } 
                else 
                {
                    if (!isset($_SESSION[$session_arg])) 
                    {
                        self::handleResponse($redirect);
                    }
                }
            }
        }

        public static function checkProtectedArea(bool $redirect = true)
        {   
            if (session_status() === PHP_SESSION_NONE) 
                session_start();

            self::check($redirect, 'SIGNED_IN');

            $session = new SessionModel(session_token: $_SESSION['SESSION_TOKEN']);
            $session_expired = $session->isExpired_by_sessionToken();
            
            if ($session_expired === 1)
            {
                self::handleResponse($redirect);
            }
            else if ($session_expired === 0)
            {
                if (isset($_SESSION['AUTH_BY_COOKIE']))
                {
                    $master_key_plaintext = UserKeysController::getMasterKeyByUserIDSessionToken(
                        $_SESSION['ID_USER'],
                        $_SESSION['SESSION_TOKEN']
                    );
                    
                    $_SESSION['MASTER_KEY'] = $master_key_plaintext;
                }
            }
            else
            {
                HttpResponse::serverError();
            }

        }

        public static function checkSignedIn()
        {
            if (session_status() === PHP_SESSION_NONE) 
                session_start();

            if (isset($_COOKIE['session_token']))
            {
                $session = new SessionModel(session_token: $_COOKIE['session_token']);

                $userID = $session->sel_userID_by_sessionToken();

                if ($userID !== -1 && $userID !== false)
                {
                    $user = new UserModel(id_user: $userID);

                    $user->sel_email_by_userID();   

                    $_SESSION['USER_DIR'] = FileSysHandler::getUserDirName($user->getUserID(), $user->getEmail());

                    $_SESSION['SIGNED_IN'] = true;
                    $_SESSION['ID_USER'] = $user->getUserID();
                    $_SESSION['SESSION_TOKEN'] = $session->getSessionToken();

                    $_SESSION['AUTH_BY_COOKIE'] = true;
 
                    HttpResponse::redirect('/clouddrive');         
                } 
            }
            else
            {
                if (isset($_SESSION['SESSION_TOKEN']))
                {
                    HttpResponse::redirect('/clouddrive');
                }
            }
        }
    }

?>