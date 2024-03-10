<?php

    class HttpResponse
    {
        private const DOWNLOAD_CHUNK_SIZE = 1000000; // 1MB

        private const CTS = 
        [
            'JSON' => 'application/json; charset=utf-8',
            'TEXT' => 'text/plain; charset=utf-8',
            'HTML' => 'text/html; charset=UTF-8',
            'OCTET_STREAM'  => 'application/octet-stream',
        ];

        private const HTTP_RESPONSE_STATUS_CODES =
        [
            200 => "OK",
            201 => "Created",
            204 => "No Content",

            400 => "Bad Request",
            401 => "Unauthorized",
            403 => "Forbidden",
            404 => "Not Found",
            405 => "Method Not Allowed",
            429 => "Too Many Requests",
            
            500 => "Internal Server Error",
            501 => "Not Implemented"
        ];

        public static function clientError(int $status_code = 400, $status_msg = false, array $response_array = array())
        {
            if (!self::statusCodeValid($status_code, 400)) 
                self::clientError(400);
            
            http_response_code($status_code);

            if ($status_msg === false)
                $status_msg = self::getStatusMsg($status_code);

            if (isset($array['redirect']))
                $array['redirect'] = $_ENV['APP_URL'] . $array['redirect'];

            $json = json_encode(
                array_merge(
                    array(
                        'success' => false,
                        'status_code' => $status_code,
                        'status_message' => $status_msg
                    ), 
                    $response_array
                ), 
                JSON_PRETTY_PRINT
            );

            header('Content-Type: ' . self::CTS['JSON']);

            echo $json;
            exit;
        }

        public static function serverError(int $status_code = 500, $status_msg = false, array $response_array = array())
        {
            if (!self::statusCodeValid($status_code, 500)) 
                self::serverError(500);

            http_response_code($status_code);

            if ($status_msg === false)
                $status_msg = self::getStatusMsg($status_code);

            $json = json_encode(
                array_merge(
                    array(
                        'success' => false,
                        'status_code' => $status_code,
                        'status_message' => $status_msg
                    ), 
                    $response_array
                ), 
                JSON_PRETTY_PRINT
            );

            header('Content-Type: ' . self::CTS['JSON']);

            echo $json;
            exit;
        }

        public static function successful(int $status_code = 200, $status_msg = false, array $response_array = array())
        {
            if (!self::statusCodeValid($status_code, 200)) 
                HttpResponse::serverError(500);
            
            http_response_code($status_code);

            if ($status_msg === false)
                $status_msg = self::getStatusMsg($status_code);

            if (isset($array['redirect']))
                $array['redirect'] = $_ENV['APP_URL'] . $array['redirect'];

            $json = json_encode(
                array_merge(
                    array(
                        'success' => true,
                        'status_code' => $status_code,
                        'status_message' => $status_msg
                    ), 
                    $response_array
                ), 
                JSON_PRETTY_PRINT
            );
            
            header('Content-Type: ' . self::CTS['JSON']);

            echo $json;
            exit;
        }

        public static function redirect($page)
        {
            $redirect_url = $_ENV['APP_URL'] . $page;
            header("location: ".$redirect_url);
            exit;
        }

        public static function download($file_path)
        {
            if (!file_exists($file_path))
                self::clientError(404, "File not found");

            header('Content-Description: File Transfer');
            header('Content-Type: ' . self::CTS['OCTET_STREAM']);
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . basename($file_path) . "\"");
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));

            $file = fopen($file_path, 'rb');
    
            while (!feof($file)) 
            {
                echo fread($file, self::DOWNLOAD_CHUNK_SIZE);
    
                if (ob_get_length() > 0) 
                {
                    ob_flush();
                    flush();
                }
            }
    
            fclose($file);
        }
    
        private static function statusCodeValid(int $status_code, int $id)
        {
            // for example 404 is ok bcs is between 400 ($id) and 499
            return ($status_code >= $id && $status_code <= $id + 99);
        }

        private static function getStatusMsg(int $status_code)
        {
            if (@self::HTTP_RESPONSE_STATUS_CODES[$status_code] === null)
                return "Status Message Not Available";
            else
                return self::HTTP_RESPONSE_STATUS_CODES[$status_code];
        }
    }


?>