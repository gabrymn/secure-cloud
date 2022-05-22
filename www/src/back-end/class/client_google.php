<?php
    /*
    class client_google {

        public static function get_object(){

            $client = new Google_Client();
            $client->setClientId(self::get_client_id());
            $client->setClientSecret(self::get_client_secret());
            $client->setRedirectUri(self::get_redirect_uri());
            $client->addScope('profile');
            $client->addScope('email');

            return $client;
        }

        private static function get_client_id(){
            sqlc::connect();
            $qry = "SELECT `value` FROM `PRSN_env` WHERE `key` = 'CLIENT_ID'";
            $value = sqlc::qry_exec($qry)['value'];
            return $value;
        }

        private static function get_client_secret(){
            sqlc::connect();
            $qry = "SELECT `value` FROM `PRSN_env` WHERE `key` = 'CLIENT_SECRET'";
            $value = sqlc::qry_exec($qry)['value'];
            return $value;
        }

        private static function get_redirect_uri(){
            sqlc::connect();
            $qry = "SELECT `value` FROM `PRSN_env` WHERE `key` = 'REDIRECT_URI'";
            $value = sqlc::qry_exec($qry)['value'];
            return $value;
        }
    }
    */
?>