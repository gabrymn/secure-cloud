<?php

    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/account_verify.php';

    function get_recover_routes()
    {
        $router = new Router();

        $router->GET('/recover', [], function() {

            AccountRecoveryController::render_recover_page();
        }); 

        $router->POST('/recover', ['email', 'recoverykey'], function($args) {

            AccountRecoveryController::process_rkey_check($args['email'], $args['recoverykey']);
        });
    
        $router->POST('/recover', ['pwd'], function($args) {
    
            AuthController::check(false, 'RECOVERING_ACCOUNT', 'ID_USER', 'RKEY');
            AccountRecoveryController::process_pwd_reset($args['pwd']);
        });

        return $router->getRoutes();
    }

?>