<?php

    class MyDatetime 
    {
        public const UTC_TZ = 'UTC'; // TZ of php and mariadb
        public const SERVER_DT_FORMAT = 'Y-m-d H:i:s';

        public const DT_FORMAT_24H = 'd/m/Y H:i:s';
        public const DT_FORMAT_12H = 'd/m/Y h:i:s A';
        public const DT_FORMAT_12H_AMERICA = 'm/d/Y h:i:s A';
        public const DT_FORMAT_DEFAULT = 'Y/m/d H:i:s';

        public static function get_client_dt($datetime_x, $timezone_y)
        {
            $datetime_format_x = self::SERVER_DT_FORMAT;
            $timezone_x = self::UTC_TZ;

            if (!in_array($timezone_y, timezone_identifiers_list()))
                $timezone_y = self::UTC_TZ;
            
            $datetime_format_y = self::get_client_dt_format($timezone_y);

            $datetime_x = DateTime::createFromFormat
            (
                $datetime_format_x, 
                $datetime_x, 
                new DateTimeZone($timezone_x)
            );

            if (!$datetime_x)
                return false;
        
            // Imposta il nuovo fuso orario di destinazione
            $datetime_x->setTimezone(new DateTimeZone($timezone_y));
        
            // Formatta la data nel formato di destinazione
            $datetime_y = $datetime_x->format($datetime_format_y);
        
            return $datetime_y;
        }

        private static function get_client_dt_format($tz)
        {
            if ($tz === self::UTC_TZ)
                return self::DT_FORMAT_24H;

            $continent = strtoupper(substr($tz, 0, strpos($tz, '/')));

            switch ($continent) 
            {
                case 'AMERICA':
                    return self::DT_FORMAT_12H_AMERICA;
                case 'EUROPE':
                    return self::DT_FORMAT_24H;
                default:
                    return self::DT_FORMAT_DEFAULT;
            }
        }

        /**
         * Get the current date and time in the specified format and timezone.
         *
         * @param string $date_format The format of the date and time. Defaults to 'Y-m-d H:i:s'.
         * @param string $tz          The timezone. Defaults to 'UTC'.
         *
         * @return string|false The current date and time formatted according to the specified format and timezone,
         *                     or false in case of an error.
         */

        public static function now($date_format = self::SERVER_DT_FORMAT, $tz = self::UTC_TZ) : string|bool
        {
            if (!date_default_timezone_set($tz))
                date_default_timezone_get(self::UTC_TZ);

            return date($date_format, time());
        }
    }


?>