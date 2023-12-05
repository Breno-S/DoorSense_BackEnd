DROP DATABASE IF EXISTS doorsense;

CREATE DATABASE doorsense;

USE doorsense;

CREATE TABLE Admin (
    ID_ADMIN INT(11) NOT NULL AUTO_INCREMENT,
    EMAIL_ADMIN VARCHAR(100) NOT NULL UNIQUE,
    SENHA_ADMIN VARCHAR(100) NOT NULL,
    PRIMARY KEY (ID_ADMIN)
);

CREATE TABLE Arduino (
    ID_ARDUINO INT(11) NOT NULL AUTO_INCREMENT,
    UNIQUE_ID VARCHAR(100) NOT NULL UNIQUE,
    STATUS_ARDUINO ENUM('Ativo', 'Inativo', 'Pendente') DEFAULT 'Pendente' NOT NULL,
    LAST_UPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ID_ARDUINO)
);

CREATE TABLE Sala (
    ID_SALA INT(11) NOT NULL AUTO_INCREMENT,
    NOME_SALA VARCHAR(100) NOT NULL,
    NUMERO_SALA VARCHAR(4),
    FK_ARDUINO INT(11) DEFAULT NULL UNIQUE,
    PRIMARY KEY (ID_SALA),
    FOREIGN KEY(FK_ARDUINO) REFERENCES ARDUINO (ID_ARDUINO),
    CONSTRAINT UC_NOME_NUMERO UNIQUE (NOME_SALA, NUMERO_SALA)
);

-- ----------------------------------------------------------------------------

-- Habilita o agendador de eventos
SET GLOBAL event_scheduler = ON;

-- Cria um Trigger de checagem para salas+numeros repetidos
DELIMITER //
CREATE TRIGGER sala_nome_numero_check BEFORE INSERT ON Sala
FOR EACH ROW
BEGIN
    IF NEW.NUMERO_SALA IS NULL AND EXISTS (SELECT 1 FROM Sala WHERE NOME_SALA = NEW.NOME_SALA AND NUMERO_SALA IS NULL) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Sala com mesmo nome e número já existe no banco de dados.';
    END IF;
END;
//
DELIMITER ;

-- Cria o procedimento para rodar toda vez que ocorre um evento
DELIMITER //
CREATE PROCEDURE checkActivity()
BEGIN
    DECLARE done BOOLEAN DEFAULT FALSE;
    DECLARE record_id INT;
    DECLARE last_update_time TIMESTAMP;
    DECLARE time_difference INT;

    -- Declare a cursor to fetch record IDs and last update times
    DECLARE cur CURSOR FOR
        SELECT ID_ARDUINO, LAST_UPDATE
        FROM Arduino;

    -- Declare continue handler to exit the loop when no more records
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Open the cursor
    OPEN cur;

    -- Start the loop
    record_loop: LOOP
        -- Fetch the next record
        FETCH cur INTO record_id, last_update_time;

        -- Exit the loop if no more records
        IF done THEN
            LEAVE record_loop;
        END IF;

        -- Calculate the time difference in minutes
        SET time_difference = TIMESTAMPDIFF(MINUTE, last_update_time, NOW());

        -- Update the status column based on the condition for the current record
        IF time_difference > 5 THEN
            UPDATE Arduino
            SET STATUS_ARDUINO = 'Inativo'
            WHERE ID_ARDUINO = record_id;
        END IF;

    END LOOP;

    -- Close the cursor
    CLOSE cur;
END;
//
DELIMITER ;

-- Define o evento e agenda-o para cada minuto
DELIMITER //
CREATE EVENT eventActivity
ON SCHEDULE EVERY 1 MINUTE
DO
BEGIN
    CALL checkActivity();
END
//
DELIMITER ;
