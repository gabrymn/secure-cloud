<?php

    require_once __DIR__ . '/../src/controller/otp.php';

    function get_auth2_routes()
    {
        $router = new Router();

        $router->GET('/auth2', [], function() {

            AuthController::check(true, 'OTP_CHECKING');
            OTPController::render_auth2_page();
        }); 
    
        $router->POST('/auth2', ['otp'], function($args) {
            
            AuthController::check(false, 'OTP_CHECKING');
            OTPController::processOtpChecking($args['otp']);
        });

        return $router->getRoutes();
    }

?>