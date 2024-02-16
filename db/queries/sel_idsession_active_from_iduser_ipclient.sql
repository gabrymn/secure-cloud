SELECT id FROM `sessions`
WHERE ip = ?
AND `status` = 'ACTIVE'
AND id =
(
    SELECT id_session
    FROM user_session_ref
    WHERE id_user = ?
)