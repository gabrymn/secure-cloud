<?php

    require_once __DIR__ . '/auth2.php';
    require_once __DIR__ . '/recover.php';
    require_once __DIR__ . '/signin.php';
    require_once __DIR__ . '/signup.php';
    require_once __DIR__ . '/email_verify.php';
    require_once __DIR__ . '/clouddrive.php';
    require_once __DIR__ . '/transfers.php';
    require_once __DIR__ . '/sessions.php';
    require_once __DIR__ . '/storage.php';
    require_once __DIR__ . '/profile.php';
    require_once __DIR__ . '/../src/controller/staticpages.php';
    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/signin.php';
    require_once __DIR__ . '/../src/controller/test.php';


    function get_routes($path)
    {
        switch($path)
        {
            case '/test':
            {
                $router = new Router();
                
                $router->GET('/test', [], function () {
            
                    TestController::process_test();
                });

                return $router->getRoutes();

                break;
            }

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
                return get_email_verify_routes();
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

            case '/transfers':
            {
                return get_transfers_routes();
                break;
            }

            case '/storage':
            {
                return get_storage_routes();
                break;
            }

            case '/profile':
            {
                return get_profile_routes();
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