<?php

    require_once __DIR__ . '/model.php';

    class SessionModel extends Model
    {
        private string $id_session;
        private string $ip;
        private string $os;
        private string $browser;
        private int $id_user;

        private $start;
        private $end;
        private $recent_activity;

        private const ID_SESSION_LEN = 32;

        public function __construct($id_session=null, $ip=null, $os=null, $browser=null, $start=null, $end=null, $recent_activity=null, $id_user=null)
        {
            date_default_timezone_set(parent::TZ);

            $this->setSessionID($id_session ? $id_session : parent::DEFAULT_STR);
            
            $this->setIP($ip ? $ip : parent::DEFAULT_STR);
            $this->setOS($os ? $os : parent::DEFAULT_STR);
            $this->setBrowser($browser ? $browser: parent::DEFAULT_STR);

            $this->setDateStart($start ? $start : parent::DEFAULT_STR);
            $this->setDateEnd($end ? $end : parent::DEFAULT_STR);
            $this->setRecentActivity($recent_activity ? $recent_activity : parent::DEFAULT_STR);

            $this->setUserID($id_user ? $id_user : parent::DEFAULT_INT);
        }

        public function setSessionID(string $id_session): void
        {
            if ($id_session === parent::DEFAULT_STR || strlen($id_session) !== self::ID_SESSION_LEN)
                $this->id_session = parent::DEFAULT_STR;
            else
                $this->id_session = $id_session;
        }

        public function setSessionIDRandom(): void
        {
            $id_session = $this->generateUID(self::ID_SESSION_LEN);
            $this->setSessionID($id_session);
        }

        public function getSessionID(): string
        {
            return $this->id_session;
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

        public function setDateStart($start = false): void
        {
            if (!$start)
                $start = MyDatetime::now();
                
            $this->start = $start;
        }

        public function getDateStart()
        {
            return $this->start;
        }

        // if nothing is passet, date end in equals to now
        public function setDateEnd($end = false): void
        {
            if (!$end)
                $end = MyDatetime::now();

            $this->end = $end;
        }

        public function getDateEnd()
        {
            return $this->end;
        }

        public function setRecentActivity($recent_activity = false): void
        {
            if (!$recent_activity)
                $recent_activity = MyDatetime::now();
            
            $this->recent_activity = $recent_activity;
        }

        public function getRecentActivity()
        {
            return $this->recent_activity;
        }

        public function setUserID($id_user)
        {
            $this->id_user = $id_user;
        }

        public function getUserID()
        {
            return $this->id_user;
        }

        public function toAssocArray($id_session=false, $ip=false, $os=false, $browser=false, $start=false, $end=false, $recent_activity=false, $id_user=false) : array
        {
            $params = array();
            
            if ($id_session)
                $params["id_session"] = $this->getSessionID();

            if ($ip)
                $params["ip"] = $this->getIP();

            if ($os)
                $params["os"] = $this->getOS();

            if ($browser)
                $params["browser"] = $this->getBrowser();
            
            if ($start)
                $params["start"] = $this->getDateStart();

            if ($end)
                $params["end"] = $this->getDateEnd();

            if ($recent_activity)
                $params["recent_activity"] = $this->getRecentActivity();
        
            if ($id_user)
                $params["id_user"] = $this->getUserID();

            return $params;
        }

        public static function getSessionsOf($id_user, $id_session)
        {
            $s = new SessionModel(id_user:$id_user, id_session:$id_session);
            $sessions = $s->sel_sessions_by_userID_sessionID();
            $actual_client_timezone = Client::getTimezone();

            foreach ($sessions as &$session)
            {
                $ip_info_session = Client::getIPInfoLimited($session['ip']);

                $client_date = MyDatetime::getClientDateTime($session['recent_activity'], $actual_client_timezone);
                
                $session['recent_activity'] = $client_date;

                $session = array_merge($session, $ip_info_session);

                if ($session['id_session'] === $id_session)
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

        public function ins()
        {
            $qry_1 = "INSERT INTO `sessions` (`id_session`, `ip`, `os`, `browser`, `id_user`) VALUES (:id_session, :ip, :os, :browser, :id_user)";
            $qry_2 = "INSERT INTO session_dates (`start`, `recent_activity`, `id_session`) VALUES (:start, :recent_activity, :id_session)";

            MyPDO::connect('insert');

            try 
            {
                MyPDO::beginTransaction();

                $status_qry_1 = MyPDO::qryExec($qry_1, $this->toAssocArray(id_session:true, ip:true, os:true, browser:true, id_user:true));
                $status_qry_2 = MyPDO::qryExec($qry_2, $this->toAssocArray(start:true, recent_activity:true, id_session:true));

                if ($status_qry_1 && $status_qry_2)
                {
                    MyPDO::commit();
                    return true;
                }
                else
                {
                    MyPDO::rollBack();
                    return false;
                }
            }
            catch(Exception $e)
            {
                MyPDO::rollBack();
                return $e->getMessage();
            }
        }
        
        public function sel_sessionID_by_UserID_clientIP()
        {
            $qry = (
                "SELECT id_session
                FROM sessions
                WHERE id_user = :id_user 
                AND ip = :ip
                AND id_session = 
                (
                    SELECT id_session
                    FROM session_dates
                    WHERE end IS NULL
                )"
            );
            
            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true, ip:true));

            if ($res === false)
                return false;
            else if ($res === array())
                return -1;
            else
            {
                $id_session = $res[0]['id_session'];
                $this->setSessionID($id_session);
                return $this->getSessionID();
            }
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

        public function sel_sessionCount_by_clientIP_userID()
        {
            $qry = (
                "SELECT COUNT(*) AS COUNT
                FROM `sessions`
                WHERE ip = :ip
                AND id_user = :id_user"
            );

            $res = MyPDO::qryExec($qry, $this->toAssocArray(ip:true, id_user:true));
            
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
         * The first session in the result set will be the one with id_session = :id_session,
         * followed by sessions with "session_dates.end = NULL" to prioritize active sessions.
         *
         * @return array An array containing the result of the query.
        */
        public function sel_sessions_by_userID_sessionID() : array
        {
            $qry = 
            (
                "SELECT sessions.*, session_dates.*
                FROM sessions
                JOIN session_dates ON sessions.id_session = session_dates.id_session
                WHERE sessions.id_user = :id_user
                ORDER BY 
                    CASE 
                        WHEN sessions.id_session = :id_session THEN 0 
                        ELSE 1 
                    END,
                    CASE 
                        WHEN session_dates.end IS NULL THEN 0 
                        ELSE 1 
                    END,
                    sessions.id_session"
            );

            MyPDO::connect('select');

            return MyPDO::qryExec($qry, $this->toAssocArray(id_session:true, id_user:true));
        }

        public function upd_recentActivity_by_sessionID()
        {
            $qry = "UPDATE session_dates 
            SET recent_activity = :recent_activity
            WHERE id_session = :id_session";
    
            MyPDO::connect('update');

            return MyPDO::qryExec($qry, $this->toAssocArray(id_session:true, recent_activity:true));
        }

        // This query expire only sessions of the user :id_user
        public function expire_by_sessionID_userID()
        {
            $qry = 
            "UPDATE session_dates 
            SET end = :end 
            WHERE id_session = 
            (
                SELECT id_session 
                FROM sessions 
                WHERE id_user = :id_user
                AND id_session = :id_session
            )";

            MyPDO::connect('update');

            try 
            {
                $status = MyPDO::qryExec($qry, $this->toAssocArray(id_session:true, end:true, id_user:true));
                return $status;
            }
            catch (PDOException $e)
            {
                // session already expired ('end' != NULL), the query is tryin' to update 'end' to now(), 
                //but 'end' is already setted to a value != NULL, so since there is an active SQL trigger, 
                // it throws an exception

                return -1;
            }
        }

        public function is_expired_by_sessionID() : int|bool
        {
            $qry =
            "SELECT *
            FROM sessions
            WHERE id_user = :id_user
            AND id_session =
            (
                SELECT id_session
                FROM session_dates
                WHERE id_session = :id_session
                AND end IS NULL
            )";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_session:true, id_user:true));
                
            if ($res === false)
                return false;
            else if ($res === array())
                return 1;
            else
                return 0;
        }
    }
?>