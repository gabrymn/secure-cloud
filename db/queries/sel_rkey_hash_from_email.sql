SELECT rkey_hash 
FROM user_security 
WHERE id_user = (SELECT id FROM users WHERE email = ?)