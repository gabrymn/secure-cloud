<?php

    require_once __DIR__ . '/model.php';
    require_once __DIR__ . '/../../resource/mydatetime.php';
    require_once __DIR__ . '/../../resource/storage/mypdo.php';
    require_once __DIR__ . '/../../resource/http/client.php';

    class SessionModel extends Model
    {
        private string $session_token;
        private string $ip;
        private string $os;
        private string $browser;
        private int $expired;
        private string $session_key_salt;
        private int $id_user;

        public const SESSION_TOKEN_LEN = 50;
        private const DEFAULT_EXPIRED = 0;

        public const SESSION_HRS_RANGE_STD = 2;
        public const SESSION_HRS_RANGE_KEEPSIGNED = 24*14;

        public function __construct($session_token=null, $ip=null, $os=null, $browser=null, $expired=null, $session_key_salt=null, $id_user=null)
        {
            if ($session_token === null)
                $this->setSessionToken($this->generateUID(self::SESSION_TOKEN_LEN));
            else
                $this->setSessionToken($session_token);
            
            $this->setIP($ip ? $ip : parent::DEFAULT_STR);
            $this->setOS($os ? $os : parent::DEFAULT_STR);
            $this->setBrowser($browser ? $browser : parent::DEFAULT_STR);

            $this->setExpired($expired ? $expired : self::DEFAULT_EXPIRED);

            $this->setSessionKeySalt($session_key_salt ? $session_key_salt : parent::DEFAULT_STR);

            $this->setUserID($id_user ? $id_user : parent::DEFAULT_INT);
        }

        public function setSessionToken(string $session_token): void
        {
            if ($session_token === parent::DEFAULT_STR || strlen($session_token) !== self::SESSION_TOKEN_LEN)
                $this->session_token = parent::DEFAULT_STR;
            else
                $this->session_token = $session_token;
        }

        public function setSessionTokenRandom(): void
        {
            $session_token = $this->generateUID(self::SESSION_TOKEN_LEN);
            $this->setSessionToken($session_token);
        }

        public function getSessionToken(): string
        {
            return $this->session_token;
        }

        public function setIP(string $ip): void
        {
            $this->ip = $ip;
        }

        public function getIP(): string
        {
            return $this->ip;
        }

        public function setOS(string $os): void
        {
            $this->os = $os;
        }

        public function getOS(): string
        {
            return $this->os;
        }

        public function setBrowser(string $browser): void
        {
            $this->browser = $browser;
        }

        public function getBrowser(): string
        {
            return $this->browser;
        }

        public function setUserID($id_user)
        {
            $this->id_user = $id_user;
        }

        public function getUserID()
        {
            return $this->id_user;
        }

        public function setSessionKeySalt($session_key_salt)
        {
            $this->session_key_salt = $session_key_salt;
        }

        public function getSessionKeySalt()
        {
            return $this->session_key_salt;
        }

        public function setExpired(int $expired) : void
        {
            $this->expired = $expired;
        }

        public function getExpired() : int
        {
            return $this->expired;
        }

        public function toAssocArray($session_token=false, $ip=false, $os=false, $browser=false, $expired=false, $session_key_salt=false, $id_user=false) : array
        {
            $params = array();
            
            if ($session_token)
                $params["session_token"] = $this->getSessionToken();

            if ($ip)
                $params["ip"] = $this->getIP();

            if ($os)
                $params["os"] = $this->getOS();

            if ($browser)
                $params["browser"] = $this->getBrowser();

            if ($expired)
                $params["expired"] = $this->getExpired();
            
            if ($session_key_salt)
                $params["session_key_salt"] = $this->getSessionKeySalt();

            if ($id_user)
                $params["id_user"] = $this->getUserID();

            return $params;
        }

        public function ins()
        {
            $qry = "INSERT INTO `sessions` (`session_token`, `ip`, `os`, `browser`, `expired`, `session_key_salt`,`id_user`) VALUES (:session_token, :ip, :os, :browser, :expired, :session_key_salt, :id_user)";

            MyPDO::connect('insert');

            return MyPDO::qryExec($qry, $this->toAssocArray(session_token:true, ip:true, os:true, browser:true, expired:true, session_key_salt:true, id_user:true));
        }

        public static function sel_sessions_by_userID_sessionToken_filtered($id_user, $session_token)
        {
            $s = new SessionModel(session_token:$session_token, id_user:$id_user);

            $sessions = SessionModel::sel_sessions_by_userID_sessionToken($id_user, $session_token);

            $actual_client_timezone = Client::getTimezone();

            foreach ($sessions as &$session)
            {
                $ip_info_session = Client::getIPInfoLimited($session['ip']);

                $client_date = MyDatetime::getClientDateTime($session['last_activity'], $actual_client_timezone);
                
                $session['last_activity'] = $client_date;

                $session = array_merge($session, $ip_info_session);

                if ($session['session_token'] === $s->getSessionToken())
                    $session['status'] = "Actual";
                else
                    if ($session['end'] === null)
                        $session['status'] = "Active";
                    else
                        $session['status'] = "Expired";

                unset($session['id_user']);
                unset($session['start']);
                unset($session['end']);
            }

            return $sessions;
        }


        public function sel_sessionCount_by_userID()
        {
            $qry = (
                "SELECT COUNT(*) AS COUNT
                FROM `sessions`
                WHERE id_user = :id_user"
            );

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));
            
            if ($res === false)
                return false;
            else
            {
                $count = $res[0]['COUNT'];
                return $count;
            }
        }

        /**
         * Retrieves all sessions for a specific user, ordering them by session ID.
         * The first session in the result set will be the one with session_token = :session_token,
         * followed by sessions with "session_dates.end = NULL" to prioritize active sessions.
         *
         * @return array An array containing the result of the query.
        */
        public static function sel_sessions_by_userID_sessionToken($id_user, $session_token) : array
        {
            $s = new SessionModel(session_token:$session_token, id_user:$id_user);

            $qry = 
            (
                "SELECT sessions.*, session_dates.*
                FROM sessions
                JOIN session_dates ON sessions.session_token = session_dates.session_token
                WHERE sessions.id_user = :id_user
                ORDER BY 
                    CASE 
                        WHEN sessions.session_token = :session_token THEN 0 
                        ELSE 1 
                    END,
                    CASE 
                        WHEN session_dates.end IS NULL THEN 0 
                        ELSE 1 
                    END,
                    sessions.session_token"
            );

            MyPDO::connect('select');

            return MyPDO::qryExec($qry, $s->toAssocArray(session_token:true, id_user:true));
        }

        public function expire_by_sessionToken()
        {
            $qry = ( 
                "UPDATE sessions 
                SET expired = 1 
                WHERE session_token = :session_token"
            );

            MyPDO::connect('update');

            try 
            {
                $status = MyPDO::qryExec($qry, $this->toAssocArray(session_token:true));
                return $status;
            }
            catch (PDOException $e)
            {
                return -1;
            }
        }

        public function isExpired_by_sessionToken() : int|false
        {
            $qry = (
                "SELECT sd.session_token
                FROM session_dates sd
                JOIN sessions s ON sd.session_token = s.session_token
                WHERE NOW() BETWEEN sd.start AND sd.end
                AND s.expired = 0
                AND s.session_token = :session_token"
            );

            mypdo::connect('select');

            $res = mypdo::qryExec($qry, $this->toAssocArray(session_token:true));

            if ($res === false)
                return false;
            
            if ($res === array())
                return 1;

            return 0;
        }

        public function sel_userID_by_sessionToken()
        {
            $qry = (
                "SELECT id_user
                FROM sessions
                WHERE session_token = :session_token"
            );

            mypdo::connect('select');

            $res = mypdo::qryExec($qry, $this->toAssocArray(session_token:true));

            if ($res === false)
                return false;

            if ($res === array())
                return  -1;

            $id_user = $res[0]['id_user'];
            return $id_user;
        }

        public function sel_sessionKeySalt_by_sessionToken()
        {
            $qry = "SELECT session_key_salt FROM sessions WHERE session_token = :session_token";

            myPDO::connect('select');

            $res = myPDO::qryExec($qry, $this->toAssocArray(session_token:true));

            if ($res === false)
                return false;
            
            else if ($res === array())
                return -1;
            
            else
            {
                $session_key_salt = $res[0]['session_key_salt'];
                return $session_key_salt;
            }
        }

        public function ins_sessionKeySalt_by_SessionToken()
        {
            $qry = "UPDATE sessions SET session_key_salt = :session_key_salt WHERE session_token = :session_token";

            MyPDO::connect('update');

            return MyPDO::qryExec($qry, $this->toAssocArray(session_token:true, session_key_salt:true));
        }
    }
?>