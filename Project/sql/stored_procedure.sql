USE ESQLDB;
DROP PROCEDURE IF EXISTS Registrazione_Studente;
DROP PROCEDURE IF EXISTS Registrazione_Docente;
DROP PROCEDURE IF EXISTS Eliminazione_Tabella_Esercizio;

DELIMITER |
CREATE PROCEDURE Registrazione_Studente(IN EMAIL VARCHAR(255), IN PSWD VARCHAR(255), IN NOME VARCHAR(255), IN COGNOME VARCHAR(255), IN TELEFONO INT(10), IN ANNO_IMMATRICOLAZIONE INT(4), IN CODICE VARCHAR(16))
BEGIN
	DECLARE countStudente INT DEFAULT 0;
	SET countStudente=(SELECT COUNT(*) FROM Utente JOIN Studente ON (EMAIL=EMAIL_STUDENTE) WHERE (Utente.EMAIL=EMAIL));
	IF (countStudente=0) THEN 
		INSERT INTO Utente(EMAIL, PSWD, NOME, COGNOME, TELEFONO) VALUES (EMAIL, PSWD, NOME, COGNOME, TELEFONO);
		INSERT INTO Studente(EMAIL_STUDENTE, ANNO_IMMATRICOLAZIONE, CODICE) VALUES (EMAIL, ANNO_IMMATRICOLAZIONE, CODICE);
	END IF;   
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Registrazione_Docente(IN EMAIL VARCHAR(255), IN PSWD VARCHAR(255), IN NOME VARCHAR(255), IN COGNOME VARCHAR(255), IN TELEFONO INT(10), IN NOME_DIPARTIMENTO VARCHAR(255), IN NOME_CORSO VARCHAR(16))
BEGIN
	DECLARE countDocente INT DEFAULT 0;
	SET countDocente=(SELECT COUNT(*) FROM Utente JOIN Docente ON (EMAIL=EMAIL_DOCENTE) WHERE (Utente.EMAIL=EMAIL));
	IF (countDocente=0) THEN
		INSERT INTO Utente(EMAIL, PSWD, NOME, COGNOME, TELEFONO) VALUES (EMAIL, PSWD, NOME, COGNOME, TELEFONO);
		INSERT INTO Docente(EMAIL_DOCENTE, NOME_DIPARTIMENTO, NOME_CORSO) VALUES (EMAIL, NOME_DIPARTIMENTO, NOME_CORSO);
	END IF;
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Eliminazione_Tabella_Esercizio(IN ID_TABELLA INT)
BEGIN
	DECLARE countTabella INT DEFAULT 0;
	SET countTabella=(SELECT COUNT(*) FROM Tabella_Esercizio WHERE (Tabella_Esercizio.ID=ID_TABELLA));
	IF (countTabella>0) THEN
		DELETE FROM Tabella_Esercizio WHERE (Tabella_Esercizio.ID=ID_TABELLA);
	END IF;
END ;
| 
DELIMITER ;