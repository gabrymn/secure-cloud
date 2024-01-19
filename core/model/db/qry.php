<?php

    require_once 'mypdo.php';

    # static class that contains all SQL queries
    # foreach SQL query there are a function, [FUNCTION_NAME] === [QUERY_FILE_NAME]
    
    class QRY 
    {
        public static function ins_user(&$mypdo, $user_data, $qrys_dir)
        {

            $qry_file = $qrys_dir . "ins_user.sql";

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

        public static function sel_id_from_email(&$mypdo, $email, $qrys_dir)
        {
            $qry_file = $qrys_dir . "sel_id_from_email.sql";
            
            if (!file_exists($qry_file))
                return $qry_file;
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

        public static function del_user_from_email(&$conn, $email, $qrys_dir)
        {
            $qry_file = $qrys_dir . "del_user_from_email.sql";
            if (!file_exists($qry_file))
                return $qry_file;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindAllParams(["email" => $email], $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
            
        }
    }


?>