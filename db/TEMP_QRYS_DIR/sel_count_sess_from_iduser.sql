SELECT COUNT(*) AS COUNT
FROM `sessions`
WHERE id = 
(
    SELECT id_session 
    FROM user_session_ref
    WHERE id_user = ?
)