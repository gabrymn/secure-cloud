DELIMITER //
CREATE TRIGGER before_update_sessions
BEFORE UPDATE ON sessions FOR EACH ROW
BEGIN
    IF OLD.status = 'EXPIRED' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Non è consentito aggiornare un record con stato "EXPIRED"';
    END IF;
END;
//
DELIMITER ;