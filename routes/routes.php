<?php

    require_once __DIR__ . '/root.php';
    require_once __DIR__ . '/test.php';
    require_once __DIR__ . '/signin.php';
    require_once __DIR__ . '/signup.php';
    require_once __DIR__ . '/auth2.php';
    require_once __DIR__ . '/recover.php';
    require_once __DIR__ . '/email_verify.php';
    require_once __DIR__ . '/clouddrive.php';
    require_once __DIR__ . '/transfers.php';
    require_once __DIR__ . '/sessions.php';
    require_once __DIR__ . '/storage.php';
    require_once __DIR__ . '/profile.php';

    abstract class Routes
    {
        final public static function get($path)
        {
            switch($path)
            {
                case '/':
                {
                    return root::getRoutes();

                    break;
                }

                case '/test':
                {
                    return test::getRoutes();

                    break;
                }

                case '/signin':
                {
                    return signin::getRoutes();
                }

                case '/signup':
                {
                    return signup::getRoutes();
                    break;
                }

                case '/signout':
                {
                    return signout::getRoutes();

                    break;
                }

                case '/verify':
                {
                    return verify::getRoutes();
                    break;
                }

                case '/recover':
                {
                    return recover::getRoutes();
                    break;
                }

                case '/auth2':
                {
                    return auth2::getRoutes();
                    break;
                }

                case '/clouddrive':
                {
                    return clouddrive::getRoutes();
                    break;
                }

                case '/sessions':
                {
                    return sessions::getRoutes();
                    break;
                }

                case '/transfers':
                {
                    return transfers::getRoutes();
                    break;
                }

                case '/storage':
                {
                    return storage::getRoutes();
                    break;
                }

                case '/profile':
                {
                    return profile::getRoutes();
                    break;
                }

                default:
                {
                    return array();
                    break;
                }
            }
        }
    }

?>