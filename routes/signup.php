<?php

    require_once __DIR__ . '/../src/controller/signup.php';

    function get_signup_routes()
    {
        $router = new Router();

        $router->GET('/signup', [], function() {

            SignupController::render_signup_page();
        });
    
        $router->GET('/signup/success', [], function() {
            
            StaticPagesController::render_page('signup_success');
        });

        $router->POST('/signup', ['email', 'pwd', 'name', 'surname'], function() {
        
            SignupController::process_signup
            (
                $_POST['email'], 
                $_POST['pwd'], 
                $_POST['name'], 
                $_POST['surname']
            );
        });

        return $router->getRoutes();
    }

?>