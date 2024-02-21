DELIMITER ;;

CREATE TRIGGER `bbefore_insert_session_dates` BEFORE INSERT ON `session_dates` FOR EACH ROW
BEGIN
    IF NEW.end IS NOT NULL THEN
        IF  NEW.start > NEW.recent_activity OR NEW.recent_activity >= NEW.end THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Violazione della condizione: start <= recent_activity < end';
        END IF;
    ELSE
        IF NEW.start > NEW.recent_activity THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Violazione della condizione: start <= recent_activity';
        END IF;
    END IF;
END;;

DELIMITER ;