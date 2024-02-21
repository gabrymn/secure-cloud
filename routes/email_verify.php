<?php

    require_once __DIR__ . '/../src/controller/email_verify.php';

    function get_email_verify_routes()
    {
        $router = new Router();

        $router->GET('/verify', [], function() {

            AuthController::check(true, ['VERIFY_PAGE_STATUS' => ['SIGNIN_WITH_EMAIL_NOT_VERIFIED', 'VERIFY_EMAIL_SENT']]);
            EmailVerifyController::render_verify_page();
        });

        $router->GET('/verify/sendemail', [], function() {

            AuthController::check(true, ['VERIFY_PAGE_STATUS' => 'SIGNIN_WITH_EMAIL_NOT_VERIFIED'], 'EMAIL');
            EmailVerifyController::send_email_verify_from_signin();
        });

        return $router->getRoutes();
    }

?>