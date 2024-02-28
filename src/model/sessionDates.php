<?php

    require_once __DIR__ . '/model.php';
    require_once __DIR__ . '/session.php';
    require_once __DIR__ . '/../../resource/myDateTime.php';
    require_once __DIR__ . '/../../resource/storage/myPDO.php';

    class SessionDatesModel extends Model
    {
        private $start_date;
        private $end_date;
        private $recent_activity_date;
        private string $id_session;

        public function __construct($start_date=null, $end_date=null, $recent_activity_date=null, $id_session=null)
        {
            date_default_timezone_set(parent::TZ);

            if ($start_date === null)
                $this->setStartDateNow();
            else
                $this->setStartDate($start_date);

            if ($end_date === null)
                $this->setEndDateNow();
            else
                $this->setEndDate($end_date);

            if ($recent_activity_date === null)
                $this->setRecentActivityDateNow();
            else
                $this->setRecentActivityDate($recent_activity_date);

            $this->setSessionID($id_session ? $id_session : parent::DEFAULT_STR);
        }

        public function setStartDate($start_date): void
        {
            $this->start_date = $start_date;
        }

        public function setStartDateNow()
        {
            $this->setStartDate(MyDatetime::now());
        }

        public function getStartDate()
        {
            return $this->start_date;
        }

        // if nothing is passet, date end in equals to now
        public function setEndDate($end_date): void
        {
            $this->end_date = $end_date;
        }

        public function setEndDateNow()
        {
            $this->setEndDate(MyDatetime::now());
        }

        public function getEndDate()
        {
            return $this->end_date;
        }

        public function setRecentActivityDate($recent_activity_date): void
        {
            $this->recent_activity_date = $recent_activity_date;
        }

        public function setRecentActivityDateNow()
        {
            $this->setRecentActivityDate(MyDatetime::now());
        }

        public function getRecentActivityDate()
        {
            return $this->recent_activity_date;
        }

        public function setSessionID(string $id_session): void
        {
            if ($id_session === parent::DEFAULT_STR || strlen($id_session) !== SessionModel::ID_SESSION_LEN)
                $this->id_session = parent::DEFAULT_STR;
            else
                $this->id_session = $id_session;
        }

        public function getSessionID(): string
        {
            return $this->id_session;
        }

        public function toAssocArray($start_date=false, $end_date=false, $recent_activity_date=false, $id_session=false) : array
        {
            $params = array();

            if ($start_date)
                $params["start_date"] = $this->getStartDate();

            if ($end_date)
                $params["end_date"] = $this->getEndDate();

            if ($recent_activity_date)
                $params["recent_activity_date"] = $this->getRecentActivityDate();
        
            if ($id_session)
                $params["id_session"] = $this->getSessionID();

            return $params;
        }

        public function ins() : bool
        {
            $qry = "INSERT INTO session_dates (`start_date`, `recent_activity_date`, `id_session`) VALUES (:start_date, :recent_activity_date, :id_session)";
            
            myPDO::connect('insert');

            return myPDO::qryExec($qry, $this->toAssocArray(start_date:true, recent_activity_date:true, id_session:true));
        }

        public function upd_recentActivity_by_sessionID()
        {
            $qry = "UPDATE session_dates 
            SET recent_activity_date = :recent_activity_date
            WHERE id_session = :id_session";
    
            MyPDO::connect('update');

            return MyPDO::qryExec($qry, $this->toAssocArray(id_session:true, recent_activity_date:true));
        }

        // This query expire only sessions of the user :id_user
        public function expire_by_sessionID()
        {
            $qry = 
            "UPDATE session_dates 
            SET end_date = :end_date 
            WHERE id_session = 
            (
                SELECT id_session 
                FROM sessions 
                WHERE id_session = :id_session
                /*AND id_user = :id_user*/
            )";

            MyPDO::connect('update');

            try 
            {
                $status = MyPDO::qryExec($qry, $this->toAssocArray(id_session:true, end_date:true));
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
            
            WHERE id_session =
            (
                SELECT id_session
                FROM session_dates
                WHERE id_session = :id_session
                AND end_date IS NULL
            ) 
            /*AND id_user = :id_user*/ ";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_session:true));
                
            if ($res === false)
                return false;
            else if ($res === array())
                return 1;
            else
                return 0;
        }
    }

?>