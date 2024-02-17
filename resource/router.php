<?php

    class Router 
    {
        private array $routes;
        private $notFoundCallback;

        private ?array $get_array;
        private ?array $post_array;

        public function __construct($get_array = null, $post_array = null)
        {
            $this->get_array = $get_array;
            $this->post_array = $post_array;
            $this->routes = array();
        }

        public function getRoutesNumber()
        {
            return count($this->routes);
        }

        public function getRoutes() : array
        {
            return $this->routes;
        }

        public function GET(string $path, array $args, callable $callback)
        {
            $this->addRoute('GET', $path, $args, $callback);
        }

        public function POST(string $path, array $args, callable $callback)
        {
            $this->addRoute('POST', $path, $args, $callback);
        }

        public function PUT(string $path, array $args, callable $callback)
        {
            $this->addRoute('PUT', $path, $args, $callback);
        }

        public function DELETE(string $path, array $args, callable $callback)
        {
            $this->addRoute('DELETE', $path, $args, $callback);
        }

        private function addRoute(string $method, string $path, array $args, callable $callback)  : bool
        {
            $this->routes[] = 
            [
                'method' => $method,
                'args' => $args, 
                'path' => $path, 
                'callback' => $callback
            ];

            return true;
        }

        public function setNotFoundCallback($notFoundCallback)
        {
            $this->notFoundCallback = $notFoundCallback;
        }

        public function handleRequest(string $method, string $path)
        {
            $parsed_path = parse_url($path, PHP_URL_PATH);

            $method_array = $this->get_method_array($method);

            $request_arg_keys = array_keys($method_array);

            foreach ($this->routes as $route) 
            {
                $res = $this->matchPath($route['path'], $parsed_path);

                if ($res['status'] && $route['method'] === $method && array_diff($route['args'], $request_arg_keys) === [])
                {
                    $this->sanitaize_user_inputs($method_array);
                    
                    call_user_func($route['callback'], $method_array);
                    return;
                }
            }

            call_user_func($this->notFoundCallback);
        }

        private function matchPath(string $routePath, string $requestPath) : array 
        {
            $pattern = '/^' . str_replace('/', '\/', $routePath) . '\/?$/';
            $status = (bool) preg_match($pattern, $requestPath, $matches);
            return array('status' => $status, 'matches' => $matches);
        }

        private function sanitaize_user_inputs(array &$array)
        {
            foreach ($array as $key => $val)
                $array[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
        }

        private function get_method_array($method) : array
        {
            switch ($method)
            {
                case 'GET':
                {
                    return $this->get_array;
                    break;
                }

                case 'POST':
                {
                    return $this->post_array;
                    break;
                }

                case 'PUT':
                {
                    $_PUT = array();
                    parse_str(file_get_contents('php://input'), $_PUT);
                    return $_PUT;
                    break;
                }

                case 'DELETE':
                {
                    return $this->get_array;
                    break;
                }
            }
        }
    }

?>
