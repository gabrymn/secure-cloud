<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../src/controller/auth.php';
    require_once __DIR__ . '/../src/controller/account_recovery.php';

    abstract class recover implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/recover', [], function() {

                AuthController::checkSignedIn();
                AccountRecoveryController::renderRecoverPage();
            }); 

            $router->POST('/recover', ['email', 'recoverykey'], function($args) {

                AccountRecoveryController::processRecoveryKeyCheck($args['email'], $args['recoverykey']);
            });
        
            $router->POST('/recover', ['pwd'], function($args) {
        
                AuthController::check(false, 'RECOVERING_ACCOUNT', 'ID_USER', 'RKEY');
                AccountRecoveryController::processPasswordReset($args['pwd']);
            });

            return $router->getRoutes();
        }
    }

?>