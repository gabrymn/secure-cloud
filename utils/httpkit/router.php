<?php

    class Router 
    {
        private array $routes;
        private $notfound_callback;

        private ?array $get_array;
        private ?array $post_array;
        private ?array $files_array;
        private ?array $files_array_keys;

        private const GET = 'GET';
        private const POST = 'POST';
        private const PUT = 'PUT';
        private const DELETE = 'DELETE';

        public function __construct($get_array = null, $post_array = null, $files = null)
        {
            $this->get_array = $get_array;
            $this->post_array = $post_array;
            $this->files_array = $files;
            $this->files_array_keys = $files === null ? null : array_keys($files);

            $this->routes = array();
        }

        public function addRoutes($routes)
        {
            foreach ($routes as $route)
            {
                $this->routes[] = $route;
            }
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
            $this->addRoute(self::GET, $path, $args, $callback);
        }

        public function POST(string $path, array $args, callable $callback)
        {
            $this->addRoute(self::POST, $path, $args, $callback);
        }

        public function PUT(string $path, array $args, callable $callback)
        {
            $this->addRoute(self::PUT, $path, $args, $callback);
        }

        public function DELETE(string $path, array $args, callable $callback)
        {
            $this->addRoute(self::DELETE, $path, $args, $callback);
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
            $this->notfound_callback = $notFoundCallback;
        }

        public function handleRequest(string $method, string $path)
        {
            $parsed_path = parse_url($path, PHP_URL_PATH);

            $method_array = $this->getMethodArray($method);

            $request_arg_keys = array_keys($method_array);

            foreach ($this->routes as $route) 
            {
                $res = $this->matchPath($route['path'], $parsed_path);

                if ($res['status'] && $route['method'] === $method && $this->cmpArgs($request_arg_keys, $route['args'])===true)
                {
                    $this->sanitaizeUserInputs($method_array);
                    
                    $args = $this->getOutputArgs($method_array);

                    call_user_func($route['callback'], $args);

                    return;
                }
            }

            call_user_func($this->notfound_callback);
        }

        private function matchPath(string $routePath, string $requestPath) : array 
        {
            $pattern = '/^' . str_replace('/', '\/', $routePath) . '\/?$/';
            $status = (bool) preg_match($pattern, $requestPath, $matches);
            return array('status' => $status, 'matches' => $matches);
        }

        private function sanitaizeUserInputs(array &$array)
        {
            foreach ($array as $key => $val)
                $array[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
        }

        private function getOutputArgs($method_array)
        {
            if ($this->files_array === array()) 
                return $method_array;
            if ($method_array === array())
                return $this->files_array;

            return array_merge($method_array, $this->files_array); 
        }

        private function cmpArgs($request_arg_keys, $route_args)
        {
            if ($this->files_array_keys !== [])
                $request_arg_keys = array_merge($this->files_array_keys, $request_arg_keys);

            if (count($request_arg_keys) !== count($route_args))
                return false;

            return (array_diff($request_arg_keys, $route_args) === []);
        }

        private function getMethodArray($method) : array
        {
            switch ($method)
            {
                case self::GET:
                {
                    return $this->get_array;
                    break;
                }

                case self::POST:
                {
                    return $this->post_array;
                    break;
                }

                case self::PUT:
                {
                    $_PUT = array();
                    parse_str(file_get_contents('php://input'), $_PUT);
                    return $_PUT;
                    break;
                }

                case self::DELETE:
                {
                    return $this->get_array;
                    break;
                }
            }
        }
    }

?>
