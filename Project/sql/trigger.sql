USE ESQLDB;

DROP TRIGGER IF EXISTS Inserimento_Record;
DROP TRIGGER IF EXISTS Inserimento_Opzione_Risposta;
DROP TRIGGER IF EXISTS Inserimento_Sketch_Codice;
DROP TRIGGER IF EXISTS Inserimento_Test_Concluso;
DROP TRIGGER IF EXISTS Aggiornamento_Test_InCompletamento;
DROP TRIGGER IF EXISTS Aggiornamento_Test_Concluso;
DROP TRIGGER IF EXISTS Cancellazione_Record;
DROP TRIGGER IF EXISTS Cancellazione_Opzione_Risposta;
DROP TRIGGER IF EXISTS Cancellazione_Sketch_Codice;

DELIMITER |
CREATE TRIGGER Inserimento_Record
AFTER INSERT ON Manipolazione_Riga
FOR EACH ROW
    UPDATE Tabella_Esercizio SET NUM_RIGHE=NUM_RIGHE+1 WHERE (ID=NEW.ID_TABELLA);
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER Inserimento_Opzione_Risposta
AFTER INSERT ON Opzione_Risposta
FOR EACH ROW
    UPDATE Quesito SET NUM_RISPOSTE=NUM_RISPOSTE+1 WHERE (ID=NEW.ID_DOMANDA_CHIUSA);
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER Inserimento_Sketch_Codice
AFTER INSERT ON Sketch_Codice
FOR EACH ROW
    UPDATE Quesito SET NUM_RISPOSTE=NUM_RISPOSTE+1 WHERE (ID=NEW.ID_DOMANDA_CODICE);
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER Inserimento_Test_Concluso
AFTER INSERT ON Risposta
FOR EACH ROW
BEGIN
    DECLARE countQuesiti INT DEFAULT 0;
    DECLARE countRisposteCorrette INT DEFAULT 0;
    SET countQuesiti = (SELECT COUNT(*) FROM Quesito WHERE (Quesito.TITOLO_TEST=NEW.TITOLO_TEST));
    SET countRisposteCorrette = (SELECT COUNT(*) FROM Risposta WHERE (Risposta.TITOLO_TEST=NEW.TITOLO_TEST) AND (Risposta.EMAIL_STUDENTE=NEW.EMAIL_STUDENTE) AND (Risposta.ESITO=1));
    IF (countQuesiti=countRisposteCorrette) THEN 
        UPDATE Completamento SET Completamento.STATO='CONCLUSO', Completamento.DATA_PRIMARISPOSTA=NOW() WHERE (Completamento.TITOLO_TEST=NEW.TITOLO_TEST) AND (Completamento.EMAIL_STUDENTE=NEW.EMAIL_STUDENTE);
    END IF; 
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER Aggiornamento_Test_InCompletamento
AFTER INSERT ON Risposta
FOR EACH ROW
BEGIN
    UPDATE Completamento SET Completamento.STATO='INCOMPLETAMENTO', Completamento.DATA_PRIMARISPOSTA=NOW() WHERE (Completamento.TITOLO_TEST=NEW.TITOLO_TEST) AND (Completamento.EMAIL_STUDENTE=NEW.EMAIL_STUDENTE);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER Aggiornamento_Test_Concluso
AFTER UPDATE ON Risposta
FOR EACH ROW
BEGIN
    DECLARE countQuesiti INT DEFAULT 0;
    DECLARE countRisposteCorrette INT DEFAULT 0;
    SET countQuesiti = (SELECT COUNT(*) FROM Quesito WHERE (Quesito.TITOLO_TEST=NEW.TITOLO_TEST));
    SET countRisposteCorrette = (SELECT COUNT(*) FROM Risposta WHERE (Risposta.TITOLO_TEST=NEW.TITOLO_TEST) AND (Risposta.EMAIL_STUDENTE=NEW.EMAIL_STUDENTE) AND (Risposta.ESITO=1));
    IF (countQuesiti=countRisposteCorrette) THEN 
        UPDATE Completamento SET Completamento.STATO='CONCLUSO' WHERE (Completamento.TITOLO_TEST=NEW.TITOLO_TEST) AND (Completamento.EMAIL_STUDENTE=NEW.EMAIL_STUDENTE);
    END IF; 
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER Cancellazione_Record
AFTER DELETE ON Manipolazione_Riga
FOR EACH ROW
    UPDATE Tabella_Esercizio SET NUM_RIGHE=NUM_RIGHE-1 WHERE (ID=OLD.ID_TABELLA);
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER Cancellazione_Opzione_Risposta
AFTER DELETE ON Opzione_Risposta
FOR EACH ROW
    UPDATE Quesito SET NUM_RISPOSTE=NUM_RISPOSTE-1 WHERE (ID=OLD.ID_DOMANDA_CHIUSA);
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER Cancellazione_Sketch_Codice
AFTER DELETE ON Sketch_Codice
FOR EACH ROW
    UPDATE Quesito SET NUM_RISPOSTE=NUM_RISPOSTE-1 WHERE (ID=OLD.ID_DOMANDA_CODICE);
|
DELIMITER ;