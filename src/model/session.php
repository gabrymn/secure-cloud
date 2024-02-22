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

            $this->set_id_session($id_session ? $id_session : parent::DEFAULT_STR);
            $this->set_ip($ip ? $ip : parent::DEFAULT_STR);
            $this->set_os($os ? $os : parent::DEFAULT_STR);
            $this->set_browser($browser ? $browser: parent::DEFAULT_STR);

            $this->set_start($start ? $start : parent::DEFAULT_STR);
            $this->set_end($end ? $end : parent::DEFAULT_STR);
            $this->set_recent_activity($recent_activity ? $recent_activity : parent::DEFAULT_STR);

            $this->set_id_user($id_user ? $id_user : parent::DEFAULT_INT);
        }

        public function set_id_session(string $id_session): void
        {
            if ($id_session === parent::DEFAULT_STR || strlen($id_session) !== self::ID_SESSION_LEN)
                $this->id_session = parent::DEFAULT_STR;
            else
                $this->id_session = $id_session;
        }

        public function get_id_session(): string
        {
            return $this->id_session;
        }

        public function set_ip(string $ip): void
        {
            $this->ip = $ip;
        }

        public function get_ip(): string
        {
            return $this->ip;
        }

        public function set_os(string $os): void
        {
            $this->os = $os;
        }

        public function get_os(): string
        {
            return $this->os;
        }

        public function set_browser(string $browser): void
        {
            $this->browser = $browser;
        }

        public function get_browser(): string
        {
            return $this->browser;
        }

        public function set_start($start = false): void
        {
            if (!$start)
                $start = MyDatetime::now();
                
            $this->start = $start;
        }

        public function get_start()
        {
            return $this->start;
        }

        // if nothing is passet, date end in equals to now
        public function set_end($end = false): void
        {
            if (!$end)
                $end = MyDatetime::now();

            $this->end = $end;
        }

        public function get_end()
        {
            return $this->end;
        }

        public function set_recent_activity($recent_activity = false): void
        {
            if (!$recent_activity)
                $recent_activity = MyDatetime::now();
            
            $this->recent_activity = $recent_activity;
        }

        public function get_recent_activity()
        {
            return $this->recent_activity;
        }

        public function set_id_user($id_user)
        {
            $this->id_user = $id_user;
        }

        public function get_id_user()
        {
            return $this->id_user;
        }

        public function to_assoc_array($id_session=false, $ip=false, $os=false, $browser=false, $start=false, $end=false, $recent_activity=false, $id_user=false) : array
        {
            $params = array();
            
            if ($id_session)
                $params["id_session"] = $this->get_id_session();

            if ($ip)
                $params["ip"] = $this->get_ip();

            if ($os)
                $params["os"] = $this->get_os();

            if ($browser)
                $params["browser"] = $this->get_browser();
            
            if ($start)
                $params["start"] = $this->get_start();

            if ($end)
                $params["end"] = $this->get_end();

            if ($recent_activity)
                $params["recent_activity"] = $this->get_recent_activity();
        
            if ($id_user)
                $params["id_user"] = $this->get_id_user();

            return $params;
        }

        public static function create_or_load($ip, $id_user)
        {   
            $session = new SessionModel(ip:$ip, id_user:$id_user);

            if (session_status() !== PHP_SESSION_ACTIVE) 
                session_start();

            $id_session = $session->sel_idsession_active_from_iduser_ipclient();

            if ($id_session === -1)
            {
                $id_session = $session->generate_uid(self::ID_SESSION_LEN);

                $session->set_id_session($id_session);
                $session->set_os(client::get_os());
                $session->set_browser(client::get_browser());

                $_SESSION['CURRENT_ID_SESSION'] = $session->get_id_session();

                $session->set_start();
                $session->set_recent_activity();

                $session->ins();

                return "created";
            }
            else
            {
                // load session
                $_SESSION['CURRENT_ID_SESSION'] = $id_session;

                $session->set_id_session($id_session);
                $session->set_recent_activity();
                
                $session->upd_recent_activity_from_idsess();

                return "loaded";
            }
        }

        public static function get_sessions_of($id_user, $id_session)
        {
            $s = new SessionModel(id_user:$id_user, id_session:$id_session);
            $sessions = $s->sel_sessions_from_iduser_ordby_idsess();
            $actual_client_timezone = client::get_timezone();

            foreach ($sessions as &$session)
            {
                $ip_info_session = client::get_ip_info_restr($session['ip']);

                $client_date = MyDatetime::get_client_dt($session['recent_activity'], $actual_client_timezone);
                
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

            mypdo::connect('insert');

            try 
            {
                mypdo::begin_transaction();

                $status_qry_1 = mypdo::qry_exec($qry_1, $this->to_assoc_array(id_session:true, ip:true, os:true, browser:true, id_user:true));
                $status_qry_2 = mypdo::qry_exec($qry_2, $this->to_assoc_array(start:true, recent_activity:true, id_session:true));

                if ($status_qry_1 && $status_qry_2)
                {
                    mypdo::commit();
                    return true;
                }
                else
                {
                    mypdo::roll_back();
                    return false;
                }
            }
            catch(Exception $e)
            {
                mypdo::roll_back();
                return $e->getMessage();
            }
        }
        
        public function sel_idsession_active_from_iduser_ipclient()
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
            
            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true, ip:true));

            if ($res === false)
                return false;
            else if ($res === array())
                return -1;
            else
            {
                $id_session = $res[0]['id_session'];
                $this->set_id_session($id_session);
                return $this->get_id_session();
            }
        }

        public function sel_count_sess_from_iduser()
        {
            $qry = (
                "SELECT COUNT(*) AS COUNT
                FROM `sessions`
                WHERE id_user = :id_user"
            );

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));
            
            if ($res === false)
                return false;
            else
            {
                $count = $res[0]['COUNT'];
                return $count;
            }
        }

        public function sel_count_sess_from_ipclient_iduser()
        {
            $qry = (
                "SELECT COUNT(*) AS COUNT
                FROM `sessions`
                WHERE ip = :ip
                AND id_user = :id_user"
            );

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(ip:true, id_user:true));
            
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
        public function sel_sessions_from_iduser_ordby_idsess() : array
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

            mypdo::connect('select');

            return mypdo::qry_exec($qry, $this->to_assoc_array(id_session:true, id_user:true));
        }

        public function upd_recent_activity_from_idsess()
        {
            $qry = "UPDATE session_dates 
            SET recent_activity = :recent_activity
            WHERE id_session = :id_session";
    
            mypdo::connect('update');

            return mypdo::qry_exec($qry, $this->to_assoc_array(id_session:true, recent_activity:true));
        }

        // This query expire only sessions of the user :id_user
        public function expire_from_idsess_iduser()
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

            mypdo::connect('update');

            try 
            {
                $status = mypdo::qry_exec($qry, $this->to_assoc_array(id_session:true, end:true, id_user:true));
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

        public function is_expired_from_idsess() : int|bool
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

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_session:true, id_user:true));
                
            if ($res === false)
                return false;
            else if ($res === array())
                return 1;
            else
                return 0;
        }
    }
?>