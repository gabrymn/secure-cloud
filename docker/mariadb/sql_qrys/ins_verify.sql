INSERT INTO secure_cloud.email_verify (token, expires, id_user)
VALUES (?, NOW() + INTERVAL 30 MINUTE, ?)