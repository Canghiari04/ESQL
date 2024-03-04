USE ESQLDB;
DROP PROCEDURE IF EXISTS Registrazione_Studente;
DROP PROCEDURE IF EXISTS Registrazione_Docente;
DROP PROCEDURE IF EXISTS Inserimento_Tabella_Esercizio;
DROP PROCEDURE IF EXISTS Inserimento_Manipolazione_Riga;
DROP PROCEDURE IF EXISTS Inserimento_Attributo;
DROP PROCEDURE IF EXISTS Inserimento_Quesito;
DROP PROCEDURE IF EXISTS Inserimento_Domanda_Chiusa;
DROP PROCEDURE IF EXISTS Inserimento_Domanda_Codice;
DROP PROCEDURE IF EXISTS Inserimento_Opzione_Risposta;
DROP PROCEDURE IF EXISTS Inserimento_Sketch_Codice;
DROP PROCEDURE IF EXISTS Inserimento_Risposta;
DROP PROCEDURE IF EXISTS Inserimento_Afferenza;
DROP PROCEDURE IF EXISTS Inserimento_Test;
DROP PROCEDURE IF EXISTS Inserimento_Vincolo_Integrita;
DROP PROCEDURE IF EXISTS Aggiornamento_Chiave;
DROP PROCEDURE IF EXISTS Aggiornamento_Test;
DROP PROCEDURE IF EXISTS Eliminazione_Tabella_Esercizio;
DROP PROCEDURE IF EXISTS Eliminazione_Manipolazione_Riga;
DROP PROCEDURE IF EXISTS Eliminazione_Quesito;
DROP PROCEDURE IF EXISTS Eliminazione_Test;
DROP PROCEDURE IF EXISTS Eliminazione_Opzione_Risposta;
DROP PROCEDURE IF EXISTS Eliminazione_Sketch_Codice;

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
CREATE PROCEDURE Inserimento_Manipolazione_Riga(IN ID_TABELLA INT)
BEGIN
	DECLARE countTabella INT DEFAULT 0;
	SET countTabella=(SELECT COUNT(*) FROM Tabella_Esercizio WHERE (Tabella_Esercizio.ID=ID_TABELLA));
	IF (countTabella>0) THEN 
		INSERT INTO Manipolazione_Riga(ID_TABELLA) VALUES (ID_TABELLA);
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
CREATE PROCEDURE Inserimento_Quesito(IN ID INT, IN TITOLO_TEST VARCHAR(255), IN DIFFICOLTA ENUM('BASSO', 'MEDIO', 'ALTO'), IN NUM_RISPOSTE INT, IN DESCRIZIONE VARCHAR(255))
BEGIN
	INSERT INTO Quesito(ID,TITOLO_TEST, DIFFICOLTA, NUM_RISPOSTE, DESCRIZIONE) VALUES (ID,TITOLO_TEST, DIFFICOLTA, NUM_RISPOSTE, DESCRIZIONE);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Domanda_Chiusa(IN ID_QUESITO INT, IN TITOLO_TEST VARCHAR(255)) 
BEGIN
	INSERT INTO Domanda_Chiusa(ID_DOMANDA_CHIUSA, TITOLO_TEST) VALUES (ID_QUESITO, TITOLO_TEST);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Domanda_Codice(IN ID_QUESITO INT, IN TITOLO_TEST VARCHAR(255)) 
BEGIN
	INSERT INTO Domanda_Codice(ID_DOMANDA_CODICE, TITOLO_TEST) VALUES (ID_QUESITO, TITOLO_TEST);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Opzione_Risposta(IN ID INT, IN ID_DOMANDA_CHIUSA INT, IN TITOLO_TEST VARCHAR(255), IN TESTO VARCHAR(255), IN SOLUZIONE BOOLEAN) 
BEGIN
	INSERT INTO Opzione_Risposta(ID, ID_DOMANDA_CHIUSA, TITOLO_TEST, TESTO, SOLUZIONE) VALUES (ID, ID_DOMANDA_CHIUSA, TITOLO_TEST, TESTO, SOLUZIONE);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Sketch_Codice(IN ID INT, IN ID_DOMANDA_CODICE INT, IN TITOLO_TEST VARCHAR(255), IN TESTO VARCHAR(255), IN SOLUZIONE BOOLEAN) 
BEGIN
	INSERT INTO Sketch_Codice(ID, ID_DOMANDA_CODICE, TITOLO_TEST, TESTO, SOLUZIONE) VALUES (ID, ID_DOMANDA_CODICE, TITOLO_TEST, TESTO, SOLUZIONE);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Risposta(IN EMAIL_STUDENTE VARCHAR(255), IN ID_QUESITO INT, IN TITOLO_TEST VARCHAR(255), IN TESTO VARCHAR(255), IN ESITO BOOLEAN) 
BEGIN
	INSERT INTO Risposta(EMAIL_STUDENTE, ID_QUESITO, TITOLO_TEST, TESTO, ESITO) VALUES (EMAIL_STUDENTE, ID_QUESITO, TITOLO_TEST, TESTO, ESITO);
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserimento_Afferenza(IN ID_QUESITO INT, IN TITOLO_TEST VARCHAR(255), IN ID_TABELLA INT) 
BEGIN
	INSERT INTO Afferenza(ID_QUESITO, TITOLO_TEST, ID_TABELLA) VALUES (ID_QUESITO, TITOLO_TEST, ID_TABELLA);
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

CREATE PROCEDURE Inserimento_Completamento(IN TITOLO_TEST VARCHAR(255), IN EMAIL_STUDENTE VARCHAR(255)) 
BEGIN
	DECLARE countInsert INT DEFAULT 0;
	SET countInsert = (SELECT COUNT(*) FROM Completamento WHERE (Completamento.TITOLO_TEST=TITOLO_TEST) AND (Completamento.EMAIL_STUDENTE=EMAIL_STUDENTE));      
	IF (countInsert=0) THEN
		INSERT INTO Completamento(TITOLO_TEST, EMAIL_STUDENTE, STATO) VALUES (TITOLO_TEST, EMAIL_STUDENTE, 'APERTO'); 
	END IF;
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
CREATE PROCEDURE Aggiornamento_Test(IN TITOLO VARCHAR(255))
BEGIN
	DECLARE countTest INT DEFAULT 0;
    DECLARE oldValue INT DEFAULT 0;
	SET countTest=(SELECT COUNT(*) FROM Test WHERE (Test.TITOLO=TITOLO));
	IF (countTest>0) THEN
		SET oldValue = (SELECT VISUALIZZA_RISPOSTE FROM Test WHERE Test.TITOLO=TITOLO);
    	UPDATE Test SET VISUALIZZA_RISPOSTE=((oldValue+1)%2) WHERE Test.TITOLO=TITOLO;
	END IF;
END;
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
CREATE PROCEDURE Eliminazione_Manipolazione_Riga(IN ID_TABELLA INT)
BEGIN
	DECLARE countTabella INT DEFAULT 0;
	SET countTabella=(SELECT COUNT(*) FROM Tabella_Esercizio WHERE (Tabella_Esercizio.ID=ID_TABELLA));
	IF (countTabella>0) THEN
		DELETE FROM Manipolazione_Riga WHERE (Manipolazione_Riga.ID_TABELLA=ID_TABELLA) LIMIT 1;
	END IF;
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Eliminazione_Quesito(IN ID_QUESITO INT, IN TITOLO_TEST VARCHAR(255))
BEGIN
	DECLARE countQuestion INT DEFAULT 0;
	SET countQuestion=(SELECT COUNT(*) FROM Quesito WHERE (Quesito.ID=ID_QUESITO) AND (Quesito.TITOLO_TEST=TITOLO_TEST));
	IF (countQuestion>0) THEN
		DELETE FROM Quesito WHERE (Quesito.ID=ID_QUESITO) AND (Quesito.TITOLO_TEST=TITOLO_TEST);
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

DELIMITER |
CREATE PROCEDURE Eliminazione_Opzione_Risposta(IN ID INT, IN ID_QUESITO INT, IN TITOLO_TEST VARCHAR(255))
BEGIN
	DELETE FROM Opzione_Risposta WHERE ((Opzione_Risposta.ID=ID) AND (Opzione_Risposta.ID_DOMANDA_CHIUSA=ID_QUESITO) AND (Opzione_Risposta.TITOLO_TEST=TITOLO_TEST));
END ;
| 
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Eliminazione_Sketch_Codice(IN ID INT, IN ID_QUESITO INT, IN TITOLO_TEST VARCHAR(255))
BEGIN
	DELETE FROM Sketch_Codice WHERE ((Sketch_Codice.ID=ID) AND (Sketch_Codice.ID_DOMANDA_CODICE=ID_QUESITO) AND (Sketch_Codice.TITOLO_TEST=TITOLO_TEST));
END ;
| 
DELIMITER ;