<?php   

    require_once __DIR__ . '/routesInterface.php';
    require_once __DIR__ . '/../resource/router.php';
    require_once __DIR__ . '/../src/controller/signin.php';
    require_once __DIR__ . '/../src/controller/emailVerify.php';
    require_once __DIR__ . '/../src/controller/auth.php';

    abstract class signin implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router_signin = new Router();

            $router_signin->GET('/signin', [], function() {

                AuthController::checkSignedIn();
                SigninController::renderSigninPage();
            });

            $router_signin->POST('/signin', ['email', 'pwd', 'keepsigned'], function($args) {

                SigninController::processSignin($args['email'], $args['pwd'], $args['keepsigned']);
            });
            
            $router_signin->GET('/signin', ['token'], function($args) {

                $response = EmailVerifyController::checkEmailVerifyToken($args['token']);
                
                $success_msg = $response['success_msg'];
                $error_msg = $response['error_msg'];
        
                SigninController::renderSigninPage($success_msg, $error_msg);
            });

            return $router_signin->getRoutes();
        }
    }



?>