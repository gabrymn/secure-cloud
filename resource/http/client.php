<?php

    class client
    {
        private const DEFAULT_PARAMS_VALUE = "UNKNOWN";

        public static function get_info() : array
        {
            return 
            [
                "os" => self::get_os(),
                "browser" => self::get_browser(),
                "ip" => self::get_ip(),
                "ip_info" => self::get_ip_info()
            ];
        }

        public static function get_os() : null|string
        {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : false;
            
            if (!$user_agent)
                return null;

            $os = '';

            if (strpos($user_agent, 'iPhone') !== false) 
                $os = 'iOS';
            elseif (strpos($user_agent, 'Windows') !== false) 
                $os = 'Windows';
            elseif (strpos($user_agent, 'Macintosh') !== false) 
                $os = 'Mac OS';
            elseif (strpos($user_agent, 'Linux') !== false) 
                $os = 'Linux';
            elseif (strpos($user_agent, 'Android') !== false) 
                $os = 'Android';
            else
                $os = self::DEFAULT_PARAMS_VALUE;

            return $os;
        }

        public static function get_browser() : null|string
        {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : false;

            if (!$user_agent)
                return null;

            $browser = '';

            if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident') !== false) 
                $browser = 'Internet Explorer';
            elseif (strpos($user_agent, 'Firefox') !== false) 
                $browser = 'Firefox';
            elseif (strpos($user_agent, 'Chrome') !== false) 
                $browser = 'Chrome';
            elseif (strpos($user_agent, 'Safari') !== false) 
                $browser = 'Safari';
            elseif (strpos($user_agent, 'Opera') !== false || strpos($user_agent, 'OPR') !== false) 
                $browser = 'Opera';
            elseif (strpos($user_agent, 'Edge') !== false)
                $browser = 'Microsoft Edge';
            else 
                $browser = self::DEFAULT_PARAMS_VALUE;

            return $browser;
        }

        public static function get_ip() : null|string
        {
            $ipaddress = '';

            try {
                if (getenv('HTTP_CLIENT_IP'))
                    $ipaddress = getenv('HTTP_CLIENT_IP');
                else if(getenv('HTTP_X_FORWARDED_FOR'))
                    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
                else if(getenv('HTTP_X_FORWARDED'))
                    $ipaddress = getenv('HTTP_X_FORWARDED');
                else if(getenv('HTTP_FORWARDED_FOR'))
                    $ipaddress = getenv('HTTP_FORWARDED_FOR');
                else if(getenv('HTTP_FORWARDED'))
                    $ipaddress = getenv('HTTP_FORWARDED');
                else if(getenv('REMOTE_ADDR'))
                    $ipaddress = getenv('REMOTE_ADDR');
                else
                    $ipaddress = self::DEFAULT_PARAMS_VALUE;
            } 
            catch (Exception $e)
            {
                $ipaddress = null;
            }
            
            return $ipaddress;
        }

        public static function get_ip_info($ip = null) : null|array
        {
            if (!$ip)  
                $ip = self::get_ip();

            if (!$ip)
                return null;

            $api_url = "https://ipinfo.io/{$ip}/json";
            $ip_info = json_decode(file_get_contents($api_url), true);

            return $ip_info;
        }

        public static function get_timezone($ip = null)
        {
            if (!$ip)  
                $ip = self::get_ip();

            if (!$ip)
                return null;

            $api_url = "https://ipinfo.io/{$ip}/timezone";
            $timezone = file_get_contents($api_url);

            return trim($timezone);
        }

        public static function get_ip_info_restr($ip = null) : null|array
        {
            if (!$ip)  
                $ip = self::get_ip();

            if (!$ip)
                return null;
            
            $ip_info = self::get_ip_info($ip);

            unset($ip_info['readme']);
            unset($ip_info['timezone']);
            unset($ip_info['postal']);
            unset($ip_info['hostname']);
            unset($ip_info['org']);
            unset($ip_info['loc']);

            return $ip_info;
        }
    }

?>