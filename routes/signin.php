<?php   

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../resource/router.php';
    require_once __DIR__ . '/../core/controller/signin.php';
    require_once __DIR__ . '/../core/controller/email_verify.php';
    require_once __DIR__ . '/../core/controller/auth.php';

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