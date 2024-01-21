<?php

    require_once 'mypdo.php';

    # static class that contains all SQL queries
    # foreach SQL query there are a function, [FUNCTION_NAME] === [QUERY_FILE_NAME]
    
    class QRY 
    {
        public static function ins_user(&$mypdo, array $user_data, $qrys_dir)
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

        public static function email_available(&$mypdo, $email, $qrys_dir)
        {
            if (QRY::sel_id_from_email($mypdo, $email, $qrys_dir) === -1)
                return 1;
            else
                return -1;
        }

        public static function sel_id_from_email(&$mypdo, $email, $qrys_dir)
        {
            $qry_file = $qrys_dir . "sel_id_from_email.sql";
            
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($email, $stmt);

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
             
                $id_user = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // no users with that email
                if ($id_user === array())
                    return -1;
                else
                    return $id_user[0]['id'];
                    // array(USER_ID) === [0] => USER_ID
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
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($email, $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
            
        }

        public static function ins_verify(&$conn, $htkn, $id_user, $qrys_dir)
        {
            $qry_file = $qrys_dir . "ins_verify.sql";
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                if (!$stmt)
                    return false;

                MYPDO::bindAllParams(array($htkn, $id_user), $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }
    }


?>