<?php   

    require_once __DIR__ . '/../resource/router.php';
    require_once __DIR__ . '/../src/controller/staticpages.php';
    require_once __DIR__ . '/../src/controller/signin.php';
    require_once __DIR__ . '/../src/controller/signup.php';
    require_once __DIR__ . '/../src/controller/session.php';
    require_once __DIR__ . '/../src/controller/account_recover.php';
    require_once __DIR__ . '/../src/controller/account_verify.php';

    $router = Router::getInstance();

    $router->addRoute(Router::HTTP_GET, '/', [], function() {

        StaticPagesController::render_home();
    });

    $router->addRoute(Router::HTTP_GET, '/signup', [], function() {

        SignupController::render_signup_page();
    });

    $router->addRoute(Router::HTTP_GET, '/signin', [], function() {

        SigninController::render_signin_page();
    });

    $router->addRoute(Router::HTTP_GET, '/recover', [], function() {

        AccountRecoveryController::render_recover_page();
    });

    $router->addRoute(Router::HTTP_GET, '/verify', [], function() {

        AccountVerifyController::render_verify_page();
    });

    $router->addRoute(Router::HTTP_POST, '/signup', ['email', 'pwd', 'name', 'surname'], function() {

        SignupController::process_signup
        (
            $_POST['email'], 
            $_POST['pwd'], 
            $_POST['name'], 
            $_POST['surname']
        );
    });

    $router->addRoute(Router::HTTP_POST, '/signin', ['email', 'pwd'], function() {

        SigninController::process_signin($_POST['email'], $_POST['pwd']);
    });

    $router->addRoute(Router::HTTP_GET, '/signin', ['token'], function() {

        $response = AccountVerifyController::check_email_verify_token($_GET['token']);
        
        $success_msg = $response['success_msg'];
        $error_msg = $response['error_msg'];
        $redirect = $response['redirect'];

        SigninController::render_signin_page($success_msg, $error_msg, $redirect);
    });

    $router->addRoute(Router::HTTP_POST, '/auth2', ['otp'], function() {

        OTPController::processOtpChecking($_GET['otp']);
    });

    $router->addRoute(Router::HTTP_POST, '/recovery', ['email', 'rkey'], function() {

        AccountRecoveryController::process_rkey_check($_POST['email'], $_POST['rkey']);
    });

    $router->addRoute(Router::HTTP_POST, '/recovery', ['pwd'], function() {

        AccountRecoveryController::process_pwd_reset($_POST['pwd']);
    });



    $router->addRoute(Router::HTTP_GET, '/sendverifyemail', [], function() {

        AccountVerifyController::send_verify_email();
    });

    $router->addRoute(Router::HTTP_POST, '/expire_session', ['id_session'], function() {

        SessionController::expire_session($_POST['id_session']);
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