DELIMITER //
CREATE TRIGGER before_update_session_dates
BEFORE UPDATE ON session_dates
FOR EACH ROW
BEGIN
  DECLARE msg VARCHAR(255);

    IF NEW.start != OLD.start THEN
        SET msg = 'Non è consentito aggiornare la data di inizio di una sessione';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    END IF;

    IF OLD.end IS NOT NULL AND OLD.end != NEW.end THEN
        SET msg = 'Non è consentito aggiornare la data di fine sessione se già impostata';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    END IF;

    IF OLD.id_session != NEW.id_session THEN
        SET msg = 'Non è consentito aggiornare id sessione';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    END IF;

    
    IF NEW.end IS NULL THEN

        IF NEW.start > NEW.recent_activity OR NEW.end < NEW.recent_activity THEN
            SET msg = 'Violazione della condizione start <= recent_activity <= end';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
        END IF;
    
    ELSE

        IF NEW.start > NEW.recent_activity THEN
            SET msg = 'Violazione della condizione start <= recent_activity';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
        END IF;

    END IF; 
  
END;
//
DELIMITER ;
