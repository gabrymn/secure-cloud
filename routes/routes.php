<?php

    require_once __DIR__ . '/auth2.php';
    require_once __DIR__ . '/clouddrive.php';
    require_once __DIR__ . '/recover.php';
    require_once __DIR__ . '/sessions.php';
    require_once __DIR__ . '/signin.php';
    require_once __DIR__ . '/signup.php';
    require_once __DIR__ . '/verify.php';

    require_once __DIR__ . '/../src/controller/staticpages.php';
    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/signin.php';


    function get_routes($path)
    {
        switch($path)
        {
            case '/':
            {
                $router = new Router();

                $router->GET('/', [], function () {
            
                    StaticPagesController::render_page('home');
                });

                return $router->getRoutes();

                break;
            }

            case '/signin':
            {
                return get_signin_routes();
                break;
            }

            case '/signup':
            {
                return get_signup_routes();
                break;
            }

            case '/signout':
            {
                $router = new Router();
                $router->GET('/signout', [], function() {

                    AuthController::check_protectedarea();
                    SigninController::process_signout();
                });

                return $router->getRoutes();

                break;
            }

            case '/verify':
            {
                return get_verify_routes();
                break;
            }

            case '/recover':
            {
                return get_recover_routes();
                break;
            }

            
            case '/auth2':
            {
                return get_auth2_routes();
                break;
            }

            case '/clouddrive':
            {
                return get_clouddrive_routes();
                break;
            }

            case '/sessions':
            {
                return get_sessions_routes();
                break;
            }

            default:
            {
                return array();
                break;
            }
        }
    }

?>