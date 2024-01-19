<?php

    require_once 'mypdo.php';

    # static class that contains all SQL queries
    
    class QRY 
    {
        public static function sel_user_by_email(&$mypdo)
        {
            $qlink = include 'qry_link.php';
            $qry_file = $qlink['SEL_USER_BY_EMAIL'];
            unset($qlink);

            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                
                if (!$stmt)
                    return false;

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
             
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $users;
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }
        
        public static function ins_user(&$mypdo, $user_data)
        {
            $qlink = include 'qry_link.php';
            $qry_file = $qlink['INS_USER'];
            unset($qlink);

            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                if (!$stmt)
                    return false;

                MYPDO::bindAllParams($user_data, $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        public static function sel_id_from_email(&$mypdo, $email)
        {
            $qlink = include 'qry_link.php';
            $qry_file = $qlink['SEL_ID_FROM_EMAIL'];
            unset($qlink);

            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindAllParams(["email" => $email], $stmt);

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
             
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return [count($users)];
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }
    }


?>