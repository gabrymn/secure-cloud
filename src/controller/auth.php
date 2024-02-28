<?php

    require_once __DIR__ . '/../../resource/http/httpResponse.php';
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

            self::check($redirect, 'LOGGED');
            
            $sd = new SessionDatesModel
            (
                id_session: $_SESSION['CURRENT_ID_SESSION'], 
            );
            
            $session_expired = $sd->is_expired_by_sessionID();
            
            if ($session_expired === 1)
            {
                self::handleResponse($redirect);
            }
        }
    }

?>