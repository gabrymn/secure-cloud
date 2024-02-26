<?php

    require_once __DIR__ . '/routesInterface.php';
    require_once __DIR__ . '/../src/controller/otp.php';

    abstract class auth2 implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/auth2', [], function() {
    
                AuthController::check(true, 'OTP_CHECKING');
                OTPController::renderAuth2Page();
            }); 
        
            $router->POST('/auth2', ['otp'], function($args) {
                
                AuthController::check(false, 'OTP_CHECKING');
                OTPController::processOTPChecking($args['otp']);
            });
    
            return $router->getRoutes();
        }
    }

?>