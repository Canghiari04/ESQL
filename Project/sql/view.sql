USE ESQLDB;
DROP VIEW IF EXISTS test_completati;
DROP VIEW IF EXISTS risposte_corrette;
DROP VIEW IF EXISTS risposte_inserite;

/* prima vista */
CREATE VIEW test_completati (CODICE, NUMERO) AS
    SELECT CODICE, COUNT(*) AS NUMERO
    FROM Studente, Completamento
    WHERE (Studente.EMAIL_STUDENTE = Completamento.EMAIL_STUDENTE) 
		AND (Completamento.STATO = 'CONCLUSO')
	GROUP BY CODICE
    ORDER BY NUMERO DESC;
   
/* seconda vista (va provata non so se va)*/
CREATE VIEW risposte_corrette (CODICE, NUMEROCORR, NUMERORIS, PERC) AS
    SELECT CODICE, 
    SUM(CASE WHEN ESITO = TRUE THEN 1 ELSE 0 END) AS NUMEROCORR,
    COUNT(*) AS NUMERORIS,
    (SUM(CASE WHEN ESITO = TRUE THEN 1 ELSE 0 END) / COUNT(*)) AS PERC
    FROM Studente, Risposta
    WHERE (Studente.EMAIL_STUDENTE = Risposta.EMAIL_STUDENTE)
    GROUP BY CODICE
    ORDER BY PERC DESC;

/* terza vista (io mostro ID quesito, ma sarebbe figo fare un hover col mouse sull'id nella
classifica e far vedere la descrizione, ovvero il testo della domanda, del singolo quesito)*/
CREATE VIEW risposte_inserite (ID_QUESITO, NUMERO) AS
    SELECT ID_QUESITO, COUNT(*) AS NUMERO
    FROM Risposta
    GROUP BY ID_QUESITO
    ORDER BY NUMERO DESC;