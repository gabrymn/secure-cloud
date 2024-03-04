<?php

    require_once __DIR__ . '/model.php';
    require_once __DIR__ . '/session.php';
    require_once __DIR__ . '/../../resource/myDateTime.php';
    require_once __DIR__ . '/../../resource/storage/myPDO.php';

    class SessionDatesModel extends Model
    {
        private $start;
        private $end;
        private $last_activity;
        private string $session_token;

        public function __construct($start=null, $end=null, $last_activity=null, $session_token=null)
        {
            date_default_timezone_set(parent::TZ);

            if ($start === null)
                $this->setStartNow();
            else
                $this->setStart($start);

            if ($end === null)
                $this->setEndNow();
            else
                $this->setEnd($end);

            if ($last_activity === null)
                $this->setLastActivityNow();
            else
                $this->setLastActivity($last_activity);

            $this->setSessionToken($session_token ? $session_token : parent::DEFAULT_STR);
        }

        public function setStart($start): void
        {
            $this->start = $start;
        }

        public function setStartNow()
        {
            $this->setStart(MyDatetime::now());
        }

        public function getStart()
        {
            return $this->start;
        }

        // if nothing is passet, date end in equals to now
        public function setEnd($end): void
        {
            $this->end = $end;
        }

        public function setEndNow()
        {
            $this->setEnd(MyDatetime::now());
        }

        public function getEnd()
        {
            return $this->end;
        }

        public function setLastActivity($last_activity): void
        {
            $this->last_activity = $last_activity;
        }

        public function setLastActivityNow()
        {
            $this->setLastActivity(MyDatetime::now());
        }

        public function getLastActivity()
        {
            return $this->last_activity;
        }

        public function setSessionToken(string $session_token): void
        {
            if ($session_token === parent::DEFAULT_STR || strlen($session_token) !== SessionModel::SESSION_TOKEN_LEN)
            {
                $this->session_token = parent::DEFAULT_STR;
            }
            else
            {
                $this->session_token = $session_token;
            }
        }

        public function getSessionToken(): string
        {
            return $this->session_token;
        }

        public function toAssocArray($start=false, $end=false, $last_activity=false, $session_token=false) : array
        {
            $params = array();

            if ($start)
                $params["start"] = $this->getStart();

            if ($end)
                $params["end"] = $this->getEnd();

            if ($last_activity)
                $params["last_activity"] = $this->getLastActivity();
        
            if ($session_token)
                $params["session_token"] = $this->getSessionToken();

            return $params;
        }

        public function ins() : bool
        {
            $qry = "INSERT INTO session_dates (`start`, `last_activity`, `end`, `session_token`) VALUES (:start, :last_activity, :end, :session_token)";
            
            myPDO::connect('insert');

            return myPDO::qryExec($qry, $this->toAssocArray(start:true, last_activity:true, end:true, session_token:true));
        }

        public function upd_recentActivity_by_sessionToken()
        {
            $qry = "UPDATE session_dates 
            SET last_activity = :last_activity
            WHERE session_token = :session_token";
    
            MyPDO::connect('update');

            return MyPDO::qryExec($qry, $this->toAssocArray(session_token:true, last_activity:true));
        }
    }

?>