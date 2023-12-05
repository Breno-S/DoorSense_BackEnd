DROP DATABASE IF EXISTS doorsense;

CREATE DATABASE doorsense;

USE doorsense;

CREATE TABLE ADMIN (
    ID_ADMIN INT(11) NOT NULL AUTO_INCREMENT,
    EMAIL_ADMIN VARCHAR(100) NOT NULL UNIQUE,
    SENHA_ADMIN VARCHAR(100) NOT NULL,
    PRIMARY KEY (ID_ADMIN)
);

CREATE TABLE ARDUINO (
    ID_ARDUINO INT(11) NOT NULL AUTO_INCREMENT,
    UNIQUE_ID VARCHAR(100) NOT NULL UNIQUE,
    STATUS_ARDUINO ENUM('Ativo', 'Inativo', 'Pendente') DEFAULT 'Pendente' NOT NULL,
    LAST_UPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ID_ARDUINO)
);

CREATE TABLE SALA (
    ID_SALA INT(11) NOT NULL AUTO_INCREMENT,
    NOME_SALA VARCHAR(100) NOT NULL,
    NUMERO_SALA VARCHAR(4),
    FK_ARDUINO INT(11) DEFAULT NULL UNIQUE,
    PRIMARY KEY (ID_SALA),
    FOREIGN KEY(FK_ARDUINO) REFERENCES ARDUINO (ID_ARDUINO),
    CONSTRAINT UC_NOME_NUMERO UNIQUE (NOME_SALA, NUMERO_SALA)
);

DELIMITER //
CREATE TRIGGER sala_nome_numero_check BEFORE INSERT ON SALA
FOR EACH ROW
BEGIN
    IF NEW.NUMERO_SALA IS NULL AND EXISTS (SELECT 1 FROM SALA WHERE NOME_SALA = NEW.NOME_SALA AND NUMERO_SALA IS NULL) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Sala com mesmo nome e número já existe no banco de dados.';
    END IF;
END;
//
DELIMITER ;
