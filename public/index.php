<?php   

    require_once __DIR__ . '/../resource/router.php';
    require_once __DIR__ . '/../routes/routes.php';

    $router = new Router($_GET, $_POST, $_FILES);

    $router->addRoutes
    (
        array_merge
        (
            Routes::get('/'),
            Routes::get('/test'),
            Routes::get('/signin'),
            Routes::get('/signup'),
            Routes::get('/signout'),
            Routes::get('/recover'),
            Routes::get('/verify'),
            Routes::get('/auth2'),
            Routes::get('/clouddrive'),
            Routes::get('/sessions'),
            Routes::get('/transfers'),
            Routes::get('/storage'),
            Routes::get('/profile')
        )
    );
    
    $router->setNotFoundCallback(function () {
        http_response_code(404);
        echo "error 404";
        exit;
    });

    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['REQUEST_URI'];   

    $router->handleRequest($method, $path);

?>