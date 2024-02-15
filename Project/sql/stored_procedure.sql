USE ESQLDB;
DROP PROCEDURE IF EXISTS Registrazione_Studente;
DROP PROCEDURE IF EXISTS Registrazione_Docente;
DROP PROCEDURE IF EXISTS Inserimento_Tabella_Esercizio;
DROP PROCEDURE IF EXISTS Inserimento_Attributo;
DROP PROCEDURE IF EXISTS Inserimento_Quesito;
DROP PROCEDURE IF EXISTS Inserimento_Test;
DROP PROCEDURE IF EXISTS Inserimento_Vincolo_Integrita;
DROP PROCEDURE IF EXISTS Aggiornamento_Chiave;
DROP PROCEDURE IF EXISTS Eliminazione_Tabella_Esercizio;
DROP PROCEDURE IF EXISTS Eliminazione_Tabella;
DROP PROCEDURE IF EXISTS Eliminazione_Quesito;
DROP PROCEDURE IF EXISTS Eliminazione_Test;

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
CREATE PROCEDURE Inserimento_Tabella_Esercizio(IN NOME VARCHAR(255), IN DATA_CREAZIONE DATETIME, IN NUM_RIGHE INT, IN EMAIL_DOCENTE VARCHAR(255))
BEGIN
	DECLARE countTabella INT DEFAULT 0;
	SET countTabella=(SELECT COUNT(*) FROM Tabella_Esercizio WHERE (Tabella_Esercizio.NOME=NOME));
	IF (countTabella=0) THEN 
		INSERT INTO Tabella_Esercizio(NOME, DATA_CREAZIONE, NUM_RIGHE, EMAIL_DOCENTE) VALUES (NOME, DATA_CREAZIONE, NUM_RIGHE, EMAIL_DOCENTE);
	END IF;   
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Attributo(IN ID_TABELLA INT, IN TIPO VARCHAR(255), IN NOME VARCHAR(255), IN CHIAVE_PRIMARIA BOOLEAN) 
BEGIN
	INSERT INTO Attributo(ID, ID_TABELLA, TIPO, NOME, CHIAVE_PRIMARIA) VALUES (NULL, ID_TABELLA, TIPO, NOME, CHIAVE_PRIMARIA);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Quesito(IN DIFFICOLTA ENUM('BASSO', 'MEDIO', 'ALTO'), IN DESCRIZIONE VARCHAR(255), IN NUM_RISPOSTE INT)
BEGIN
	INSERT INTO Quesito(ID, DIFFICOLTA, DESCRIZIONE, NUM_RISPOSTE) VALUES (NULL, DIFFICOLTA, DESCRIZIONE, NUM_RISPOSTE);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Domanda_Chiusa(IN ID_QUESITO INT) 
BEGIN
	INSERT INTO Domanda_Chiusa(ID_DOMANDA_CHIUSA) VALUES (ID_QUESITO);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Domanda_Codice(IN ID_QUESITO INT) 
BEGIN
	INSERT INTO Domanda_Codice(ID_DOMANDA_CODICE) VALUES (ID_QUESITO);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Test(IN TITOLO VARCHAR(255), IN EMAIL_DOCENTE VARCHAR(255), IN FOTO BLOB, IN DATA_CREAZIONE DATE, IN VISUALIZZA_RISPOSTE BOOLEAN) 
BEGIN
	INSERT INTO Test(TITOLO, EMAIL_DOCENTE, FOTO, DATA_CREAZIONE, VISUALIZZA_RISPOSTE) VALUES (TITOLO, EMAIL_DOCENTE, FOTO, DATA_CREAZIONE, VISUALIZZA_RISPOSTE); 
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Vincolo_Integrita(IN ID_ATTRIBUTO_REFERENZIANTE INT, IN ID_ATTRIBUTO_REFERENZIATO INT)
BEGIN
	INSERT INTO Vincolo_Integrita(REFERENTE, REFERENZIATO) VALUES(ID_ATTRIBUTO_REFERENZIANTE, ID_ATTRIBUTO_REFERENZIATO);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Aggiornamento_Chiave(IN ID_TABELLA INT, IN NOME VARCHAR(255))
BEGIN
	UPDATE Attributo SET CHIAVE_PRIMARIA=1 WHERE (Attributo.ID_TABELLA=ID_TABELLA) AND (Attributo.NOME=NOME);
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

DELIMITER |
CREATE PROCEDURE Eliminazione_Quesito(IN ID_QUESITO INT)
BEGIN
	DECLARE countQuesito INT DEFAULT 0;
	SET countQuesito=(SELECT COUNT(*) FROM Quesito WHERE (Quesito.ID=ID_QUESITO));
	IF (countQuesito>0) THEN
		DELETE FROM Quesito WHERE (Quesito.ID=ID_QUESITO);
	END IF;
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Eliminazione_Test(IN TITOLO VARCHAR(255))
BEGIN
	DECLARE countTest INT DEFAULT 0;
	SET countTest=(SELECT COUNT(*) FROM Test WHERE (Test.TITOLO=TITOLO));
	IF (countTest>0) THEN
		DELETE FROM Test WHERE (Test.TITOLO=TITOLO);
	END IF;
END ;
| 
DELIMITER ;