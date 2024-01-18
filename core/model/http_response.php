<?php

    class http_response 
    {
        private const CT_JSON = "Content-Type: application/json; charset=utf-8";
        private const CT_TEXT = "Content-Type: text/plain; charset=utf-8";
        private const CT_HTML = "Content-Type: text/html; charset=UTF-8";

        private const HTTP_RESPONSE_STATUS_CODES = array(
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
        );

        public static function client_error(int $status_code = 400, $status_msg = false, array $array = array()){

            if (!self::status_code_valid($status_code, 400)) 
                http_response::server_error(500);

            http_response_code($status_code);

            if ($status_msg === false)
                $status_msg = self::get_status_msg($status_code);

            $json = json_encode(
                array_merge(
                    array(
                        'success' => false,
                        'status_code' => $status_code,
                        'status_message' => $status_msg
                    ), 
                    $array
                ), 
                JSON_PRETTY_PRINT
            );

            self::ctype('JSON');
            echo $json;
        }

        public static function server_error(int $status_code = 500, $status_msg = false, array $array = array()){

            if (!self::status_code_valid($status_code, 500)) 
                http_response::server_error(500);

            http_response_code($status_code);

            if ($status_msg === false)
                $status_msg = self::get_status_msg($status_code);

            $json = json_encode(
                array_merge(
                    array(
                        'success' => false,
                        'status_code' => $status_code,
                        'status_message' => $status_msg
                    ), 
                    $array
                ), 
                JSON_PRETTY_PRINT
            );

            self::ctype('JSON');
            echo $json;
        }

        public static function successful(int $status_code = 200, $status_msg = false, array $array = array()){

            if (!self::status_code_valid($status_code, 200)) 
                http_response::server_error(500);
            
            http_response_code($status_code);

            if ($status_msg === false)
                $status_msg = self::get_status_msg($status_code);

            $json = json_encode(
                array_merge(
                    array(
                        'success' => true,
                        'status_code' => $status_code,
                        'status_message' => $status_msg
                    ), 
                    $array
                ), 
                JSON_PRETTY_PRINT
            );
            
            self::ctype('JSON');
            echo $json;
        }

        public static function ctype($option){

            switch (strtoupper($option)){
                case 'TEXT': default: {   
                    header(self::CT_TEXT);
                    break;
                }
                case 'JSON': {
                    header(self::CT_JSON);
                    break;
                }
                case 'HTML': {
                    header(self::CT_HTML);
                    break;
                }
            }
        }

        private static function status_code_valid(int $status_code, int $id){
            // for example 404 is ok bcs is between 400 ($id) and 499
            return ($status_code >= $id && $status_code <= $id + 99);
        }

        private static function get_status_msg(int $status_code){

            if (@self::HTTP_RESPONSE_STATUS_CODES[$status_code] === null)
                return "Status Message Not Available";
            else
                return self::HTTP_RESPONSE_STATUS_CODES[$status_code];
        }
    }


?>