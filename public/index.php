<?php   

    require_once __DIR__ . '/../resource/router.php';
    require_once __DIR__ . '/../src/controller/staticpages.php';
    require_once __DIR__ . '/../src/controller/signin.php';
    require_once __DIR__ . '/../src/controller/signup.php';
    require_once __DIR__ . '/../src/controller/session.php';
    require_once __DIR__ . '/../src/controller/account_recover.php';
    require_once __DIR__ . '/../src/controller/account_verify.php';

    $router = new Router($_GET, $_POST);

    $router->GET('/', [], function () {

        StaticPagesController::render_home();
    });

    $router->GET('/signup', [], function() {

        SignupController::render_signup_page();
    });


    $router->GET('/signin', [], function() {

        SigninController::render_signin_page();
    });

    $router->GET('/recover', [], function() {

        AccountRecoveryController::render_recover_page();
    }); 

    $router->GET('/verify', [], function() {

        AccountVerifyController::render_verify_page();
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

    $router->POST('/signin', ['email', 'pwd'], function($args) {

        SigninController::process_signin($args['email'], $args['pwd']);
    }); 

    $router->GET('/signin', ['token'], function($args) {

        $response = AccountVerifyController::check_email_verify_token($args['token']);
        
        $success_msg = $response['success_msg'];
        $error_msg = $response['error_msg'];
        $redirect = $response['redirect'];

        SigninController::render_signin_page($success_msg, $error_msg, $redirect);
    });

    $router->POST('/auth2', ['otp'], function($args) {

        OTPController::processOtpChecking($args['otp']);
    });


    $router->POST('/recovery', ['email', 'rkey'], function($args) {

        AccountRecoveryController::process_rkey_check($args['email'], $args['rkey']);
    });

    $router->POST('/recovery', ['pwd'], function($args) {

        AccountRecoveryController::process_pwd_reset($args['pwd']);
    });

    $router->GET('/sendverifyemail', [], function() {

        AccountVerifyController::send_verify_email();
    });

    $router->POST('/expire_session', ['id_session'], function($args) {

        SessionController::expire_session($args['id_session']);
    });

    $router->setNotFoundCallback(function () {
        http_response_code(404);
        echo "error 404";
        exit;
    });

    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['REQUEST_URI'];   

    $router->handleRequest($method, $path);

?>