<?php   

    require_once __DIR__ . '/../resource/router.php';

    require_once __DIR__ . '/../routes/routes.php';

    $router = new Router($_GET, $_POST);

    $router->addRoutes
    (
        array_merge
        (
            get_routes('/'),
            get_routes('/signin'),
            get_routes('/signup'),
            get_routes('/signout'),
            get_routes('/recover'),
            get_routes('/verify'),
            get_routes('/auth2'),
            get_routes('/clouddrive'),
            get_routes('/sessions')
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