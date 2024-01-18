<?php
    function get_client_info() {
        
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $os = "Sconosciuto";
        $browser = "Sconosciuto";

        // OS
        if (strpos($userAgent, 'iPhone') !== false) {
            $os = 'iPhone';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (strpos($userAgent, 'Macintosh') !== false) {
            $os = 'Mac OS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        } elseif (strpos($userAgent, 'iOS') !== false) {
            $os = 'iOS';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $os = 'Android';
        }

        // Browser
        if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            $browser = 'Internet Explorer';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            $browser = 'Opera';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Microsoft Edge';
        }

        return array('OS/DEVICE' => $os, 'BROWSER' => $browser, "IP" => $ipAddress);
    }
?>