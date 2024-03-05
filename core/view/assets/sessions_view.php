<?php

    class SessionsView
    {
        public static function get(array $sessions)
        {
            $sessions_table = "";
            
            $sessions_table .= "<script>let sessionRefs = new Map();</script>";

            $i = 0;
            
            foreach ($sessions as $session)
            {
                $loc = $session['city'] . ', ' . $session['country'];
                
                unset($session['city']);
                unset($session['country']);

                $sessions_table .= "<tr>";
                $sessions_table .= "<td>{$session['os']}</td>";
                $sessions_table .= "<td>{$session['browser']}</td>";
                $sessions_table .= "<td>{$session['ip']}</td>";
                $sessions_table .= "<td>{$loc}</td>";
                $sessions_table .= "<td>{$session['last_activity']}</td>";
                $sessions_table .= "<td id=id_sess_status_$i>{$session['status']}</td>";
                
                if ($session['status'] !== 'Expired')
                {
                    $sessions_table .= (
                        "<td id=id_td_expire_{$i}>
                            <button onclick=expireSession({$i}) class='close-sess-btn'>Close</button>
                        </td>"
                    );
                }

                $sessions_table .= "</tr>";

                $sessions_table .= ( 
                    "<script>
                        sessionRefs.set('{$i}', '{$session['session_token']}')
                    </script>"
                );

                $i++;
            }
            
            return $sessions_table;
        }

    }

?>