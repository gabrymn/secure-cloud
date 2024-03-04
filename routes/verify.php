<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../src/controller/email_verify.php';

    abstract class verify implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/verify', [], function() {

                AuthController::checkSignedIn();
                AuthController::check(true, ['VERIFY_PAGE_STATUS' => ['SIGNIN_WITH_EMAIL_NOT_VERIFIED', 'VERIFY_EMAIL_SENT']]);
                EmailVerifyController::renderVerifyPage();
            });
    
            $router->GET('/verify/sendemail', [], function() {
                
                AuthController::checkSignedIn();
                AuthController::check(true, ['VERIFY_PAGE_STATUS' => 'SIGNIN_WITH_EMAIL_NOT_VERIFIED'], 'EMAIL');
                EmailVerifyController::sendEmailVerifyFromSignin();
            });
    
            return $router->getRoutes();
        }
    }

?>