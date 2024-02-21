<?php

    require_once __DIR__ . '/../resource/router.php';
    require_once __DIR__ . '/../src/controller/signin.php';
    require_once __DIR__ . '/../src/controller/email_verify.php';

    function get_signin_routes() : array
    {
        $router_signin = new Router();

        $router_signin->GET('/signin', [], function() {

            SigninController::render_signin_page();
        });

        $router_signin->POST('/signin', ['email', 'pwd'], function($args) {

            SigninController::process_signin($args['email'], $args['pwd']);
        });
        
        $router_signin->GET('/signin', ['token'], function($args) {

            $response = EmailVerifyController::check_email_verify_token($args['token']);
            
            $success_msg = $response['success_msg'];
            $error_msg = $response['error_msg'];
            $redirect = $response['redirect'];
    
            SigninController::render_signin_page($success_msg, $error_msg, $redirect);
        });

        return $router_signin->getRoutes();
    }



?>