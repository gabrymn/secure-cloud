UPDATE user_session_ref 
SET recent_activity = ? 
WHERE id_user = ? 
AND id_session = ? 