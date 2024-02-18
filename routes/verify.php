<?php

    function get_verify_routes()
    {
        $router = new Router();

        $router->GET('/verify', [], function() {

            AuthController::check(true, ['VERIFY_PAGE_STATUS' => ['SIGNIN_WITH_EMAIL_NOT_VERIFIED', 'VERIFY_EMAIL_SENT']]);
            AccountVerifyController::render_verify_page();
        });

        $router->GET('/verify/sendemail', [], function() {

            AuthController::check(true, ['VERIFY_PAGE_STATUS' => 'SIGNIN_WITH_EMAIL_NOT_VERIFIED'], 'EMAIL');
            AccountVerifyController::send_verify_email();
        });

        return $router->getRoutes();
    }

?>