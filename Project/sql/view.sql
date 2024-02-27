USE ESQLDB;
DROP VIEW IF EXISTS Test_Completati;
DROP VIEW IF EXISTS Risposte_Corrette;
DROP VIEW IF EXISTS Risposte_Inserite;

CREATE VIEW Test_Completati (CODICE, NUMERO) AS
    SELECT CODICE, COUNT(*) AS NUMERO
    FROM Studente, Completamento
    WHERE (Studente.EMAIL_STUDENTE = Completamento.EMAIL_STUDENTE) 
		AND (Completamento.STATO = 'CONCLUSO')
	GROUP BY CODICE
    ORDER BY NUMERO DESC;
   
CREATE VIEW Risposte_Corrette (CODICE, NUMEROCORR, NUMERORIS, PERC) AS
    SELECT CODICE, 
    SUM(CASE WHEN ESITO = TRUE THEN 1 ELSE 0 END) AS NUMEROCORR,
    COUNT(*) AS NUMERORIS,
    (SUM(CASE WHEN ESITO = TRUE THEN 1 ELSE 0 END) / COUNT(*)) AS PERC
    FROM Studente, Risposta
    WHERE (Studente.EMAIL_STUDENTE = Risposta.EMAIL_STUDENTE)
    GROUP BY CODICE
    ORDER BY PERC DESC;

CREATE VIEW Risposte_Inserite (ID_QUESITO, NUMERO) AS
    SELECT ID_QUESITO, COUNT(*) AS NUMERO
    FROM Risposta
    GROUP BY ID_QUESITO
    ORDER BY NUMERO DESC;