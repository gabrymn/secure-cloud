<?php

    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/crypto.php';
    require_once __DIR__ . '/../../resource/token.php';
    require_once __DIR__ . '/../../resource/client.php';
    require_once __DIR__ . '/../model/session.php';
    
    class AuthController
    {
        private const REDIRECT_PAGE = '/signin';

        private static function handle_response(bool $redirect)
        {
            session_destroy();

            if ($redirect)
                http_response::redirect(self::REDIRECT_PAGE);
            else
                http_response::client_error(401);
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
                                self::handle_response($redirect);
                        }
                        else
                        {
                            if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $value) 
                            {
                                self::handle_response($redirect);
                            }
                        }
                    }
                } 
                else 
                {
                    if (!isset($_SESSION[$session_arg])) 
                    {
                        self::handle_response($redirect);
                    }
                }
            }
        }

        public static function check_protectedarea(bool $redirect = true)
        {   
            if (session_status() === PHP_SESSION_NONE) 
                session_start();

            self::check($redirect, 'LOGGED');
    
            $s = new Session
            (
                id_session: $_SESSION['CURRENT_ID_SESSION'], 
                id_user: $_SESSION['ID_USER']
            );
            
            $session_expired = $s->is_expired_from_idsess();
            
            if ($session_expired)
            {
                self::handle_response($redirect);
            }
        }
    }

?>