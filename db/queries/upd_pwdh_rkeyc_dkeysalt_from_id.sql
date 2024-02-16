UPDATE user_security 
SET pwd_hash = ?, rkey_c = ?, dkey_salt = ? 
WHERE id_user = ?