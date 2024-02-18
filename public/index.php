<?php   

    require_once __DIR__ . '/../resource/router.php';
    require_once __DIR__ . '/../src/controller/staticpages.php';
    require_once __DIR__ . '/../src/controller/signin.php';
    require_once __DIR__ . '/../src/controller/signup.php';
    require_once __DIR__ . '/../src/controller/session.php';
    require_once __DIR__ . '/../src/controller/account_recover.php';
    require_once __DIR__ . '/../src/controller/account_verify.php';
    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/clouddrive.php';
    require_once __DIR__ . '/../src/controller/otp.php';

    $router = new Router($_GET, $_POST);

    $router->GET('/', [], function () {

        StaticPagesController::render_page('home');
    });

    $router->GET('/signup', [], function() {

        SignupController::render_signup_page();
    });

    $router->GET('/signup/success', [], function() {
        
        StaticPagesController::render_page('signup_success');
    });

    $router->GET('/signin', [], function() {

        SigninController::render_signin_page();
    });

    $router->GET('/auth2', [], function() {

        AuthController::check(true, 'OTP_CHECKING');
        OTPController::render_auth2_page();
    }); 

    $router->GET('/recover', [], function() {

        AccountRecoveryController::render_recover_page();
    }); 

    $router->GET('/clouddrive', [], function() {

        AuthController::check_protectedarea();
        CloudDriveController::render_clouddrive_page();
    }); 

    $router->GET('/verify', [], function() {

        AuthController::check(true, ['VERIFY_PAGE_STATUS' => ['SIGNIN_WITH_EMAIL_NOT_VERIFIED', 'VERIFY_EMAIL_SENT']]);
        AccountVerifyController::render_verify_page();
    });

    $router->GET('/signout', [], function() {

        AuthController::check_protectedarea();
        SigninController::process_signout();
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
        
        AuthController::check(false, 'OTP_CHECKING');
        OTPController::processOtpChecking($args['otp']);
    });


    $router->POST('/recover', ['email', 'recoverykey'], function($args) {

        AccountRecoveryController::process_rkey_check($args['email'], $args['recoverykey']);
    });

    $router->POST('/recover', ['pwd'], function($args) {

        AuthController::check(false, 'RECOVERING_ACCOUNT', 'ID_USER', 'RKEY');
        AccountRecoveryController::process_pwd_reset($args['pwd']);
    });

    $router->GET('/sendverifyemail', [], function() {

        AuthController::check(true, ['VERIFY_PAGE_STATUS' => 'SIGNIN_WITH_EMAIL_NOT_VERIFIED'], 'EMAIL');
        AccountVerifyController::send_verify_email();
    });

    $router->POST('/expire_session', ['id_session'], function($args) {

        AuthController::check_protectedarea(false);
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