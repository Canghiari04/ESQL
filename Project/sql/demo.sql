USE ESQLDB;

INSERT  INTO Utente VALUES("mario.rossi@gmail.com", "1234", "Mario", "Rossi", NULL);
INSERT  INTO Utente VALUES("davide.bianchi@gmail.com", "1234", "Davide", "Bianchi", NULL);
INSERT  INTO Utente VALUES("luigi.bianchi@gmail.com", "1234", "Luigi", "Bianchi", "3386119667");
INSERT  INTO Studente VALUES("mario.rossi@gmail.com", 2021 , "001");
INSERT  INTO Studente VALUES("davide.bianchi@gmail.com", 2022 , "002");
INSERT  INTO Docente VALUES("luigi.bianchi@gmail.com", "Informatica" , "Algoritmi");

CREATE TABLE CIRCOLO(
NOME VARCHAR(255) PRIMARY KEY,
CITTA VARCHAR(255),
NUM_CAMPI_TENNIS INT
);

CREATE TABLE SOCIO(
CF VARCHAR(255) PRIMARY KEY,
NOME_CIRCOLO VARCHAR(255) REFERENCES CIRCOLO(NOME) ON DELETE CASCADE,
NOME VARCHAR(255),
COGNOME VARCHAR(255),
ANNO_NASCITA INT
);

CREATE TABLE TELEFONO(
NUMERO VARCHAR(10) PRIMARY KEY,
CF_SOCIO VARCHAR(255) REFERENCES SOCIO(CF)
);

INSERT INTO Tabella_Esercizio VALUES(1, "CIRCOLO", "2008-10-29 14:56:59", 6, "luigi.bianchi@gmail.com");
INSERT INTO Tabella_Esercizio VALUES(2, "SOCIO", "2008-10-29 14:56:59", 8, "luigi.bianchi@gmail.com");
INSERT INTO Tabella_Esercizio VALUES(3, "TELEFONO", "2008-10-29 14:56:59", 11, "luigi.bianchi@gmail.com");

INSERT INTO Attributo VALUES(1, 1, "VARCHAR", "NOME", 1);
INSERT INTO Attributo VALUES(2, 1, "VARCHAR", "CITTA", 0);
INSERT INTO Attributo VALUES(3, 1, "INT", "NUM_CAMPI_TENNIS", 0);

INSERT INTO Attributo VALUES(4, 2, "VARCHAR", "CF", 1); 
INSERT INTO Attributo VALUES(5, 2, "VARCHAR", "NOME_CIRCOLO", 0);
INSERT INTO Attributo VALUES(6, 2, "VARCHAR", "NOME", 0);
INSERT INTO Attributo VALUES(7, 2, "VARCHAR", "COGNOME", 0);
INSERT INTO Attributo VALUES(8, 2, "INT", "ANNO_NASCITA", 0);

INSERT INTO Attributo VALUES(9, 3,  "VARCHAR", "NUMERO",  1);
INSERT INTO Attributo VALUES(10, 3, "VARCHAR", "CF_SOCIO", 0);

INSERT INTO Vincolo_Integrita VALUES(5, 1);
INSERT INTO Vincolo_Integrita VALUES(10, 4);

INSERT INTO CIRCOLO VALUES("Supertennis", "Bologna", 10);
INSERT INTO CIRCOLO VALUES("Tennis 2000", "Bologna", 7);
INSERT INTO CIRCOLO VALUES("GiardiniMargherita", "Bologna", 5);
INSERT INTO CIRCOLO VALUES("TennisClub", "Rimini", 4);
INSERT INTO CIRCOLO VALUES("TennisWorld", "Modena", 6);
INSERT INTO CIRCOLO VALUES("TennisLandia", "Piacenza", 2);

INSERT INTO SOCIO VALUES("MR90", "Supertennis", "Mario", "Rossi", 1990);
INSERT INTO SOCIO VALUES("MB95", "TennisWorld", "Michela", "Bianchi", 1995);
INSERT INTO SOCIO VALUES("AN10", "Tennis 2000", "Andrea", "Neri", 2010);
INSERT INTO SOCIO VALUES("DV05", "GiardiniMargherita", "Daniela", "Viola", 2005);
INSERT INTO SOCIO VALUES("MA10", "TennisWorld", "Marco", "Arancio", 2010);
INSERT INTO SOCIO VALUES("MR00", "Supertennis", "Maria", "Rosa", 2000);
INSERT INTO SOCIO VALUES("MN98", "Supertennis", "Maria", "Nerone", 1990);
INSERT INTO SOCIO VALUES("MR70", "GiardiniMargherita", "Mario", "Rossini", 1970);

INSERT INTO TELEFONO VALUES("05112454", "MR90");
INSERT INTO TELEFONO VALUES("05117453", "MR90");
INSERT INTO TELEFONO VALUES("05112455", "MB95");
INSERT INTO TELEFONO VALUES("05112356", "AN10");
INSERT INTO TELEFONO VALUES("05112154", "DV05");
INSERT INTO TELEFONO VALUES("05111354", "MA10");
INSERT INTO TELEFONO VALUES("05199432", "MA10");
INSERT INTO TELEFONO VALUES("05188881", "MA10");
INSERT INTO TELEFONO VALUES("05115454", "MR00");
INSERT INTO TELEFONO VALUES("05118954", "MN98");
INSERT INTO TELEFONO VALUES("05119754", "MR70");

INSERT INTO Test VALUES("Test Completo", "luigi.bianchi@gmail.com", NULL, "2008-10-29 14:56:59", 0);

INSERT INTO Quesito VALUES(1, "Test Completo", 'MEDIO', 0, "Scrivere la query che visualizza nome e città del circolo cui è iscritto l’utente Marco Arancio");
INSERT INTO Quesito VALUES(2, "Test Completo", 'BASSO', 0, "Scrivere la query che visualizza il numero di soci nati prima del 2000 (escluso)");
INSERT INTO Quesito VALUES(3, "Test Completo", 'BASSO', 0, "Scrivere la query che calcola, per ogni circolo, il numero di soci iscritti");
INSERT INTO Quesito VALUES(4, "Test Completo", 'MEDIO', 0, "Scrivere la query che calcola Nome e Cognome dei soci che hanno almeno 2 recapiti telefonici");
INSERT INTO Quesito VALUES(5, "Test Completo", 'ALTO', 0, "Scrivere la query che calcola la città che hanno complessivamente (=considerando tutti i circoli presenti) più di 5 campi da tennis");
INSERT INTO Quesito VALUES(6, "Test Completo", 'ALTO', 0, "Scrivere la query che calcola i nomi dei circoli con più di 3 campi da tennis che NON hanno soci");
INSERT INTO Quesito VALUES(7, "Test Completo", 'MEDIO', 0, "Scrivere la query che calcola i nomi dei circoli che si trovano a Bologna oppure che hanno almeno 3 soci");
INSERT INTO Quesito VALUES(8, "Test Completo", 'MEDIO', 0, "Scrivere la query che calcola, per ogni socio iscritto ad un circolo di Bologna, il numero di recapiti telefonici a disposizione");

INSERT INTO Afferenza VALUES(1, "Test Completo", 1);
INSERT INTO Afferenza VALUES(1, "Test Completo", 2);
INSERT INTO Afferenza VALUES(2, "Test Completo", 2);
INSERT INTO Afferenza VALUES(3, "Test Completo", 2);
INSERT INTO Afferenza VALUES(4, "Test Completo", 2);
INSERT INTO Afferenza VALUES(5, "Test Completo", 1);
INSERT INTO Afferenza VALUES(6, "Test Completo", 1);
INSERT INTO Afferenza VALUES(7, "Test Completo", 1);
INSERT INTO Afferenza VALUES(8, "Test Completo", 1);
INSERT INTO Afferenza VALUES(8, "Test Completo", 2);
INSERT INTO Afferenza VALUES(8, "Test Completo", 3);

INSERT INTO Domanda_Codice VALUES(1, "Test Completo");
INSERT INTO Domanda_Codice VALUES(2, "Test Completo");
INSERT INTO Domanda_Codice VALUES(3, "Test Completo");
INSERT INTO Domanda_Codice VALUES(4, "Test Completo");
INSERT INTO Domanda_Codice VALUES(5, "Test Completo");
INSERT INTO Domanda_Codice VALUES(6, "Test Completo");
INSERT INTO Domanda_Codice VALUES(7, "Test Completo");
INSERT INTO Domanda_Codice VALUES(8, "Test Completo");

INSERT INTO Sketch_Codice VALUES(1, 1, "Test Completo", "SELECT CIRCOLO.NOME, CIRCOLO.CITTA FROM CIRCOLO, SOCIO WHERE (SOCIO.NOMECIRCOLO = CIRCOLO.NOME) AND (SOCIO.NOME = 'MARCO') AND (SOCIO.COGNOME = 'ARANCIO');", 1);
INSERT INTO Sketch_Codice VALUES(1, 2, "Test Completo", "SELECT COUNT(*) FROM SOCIO WHERE (ANNONASCITA < 2000);", 1);
INSERT INTO Sketch_Codice VALUES(1, 3, "Test Completo", "SELECT COUNT(*), NOMECIRCOLO FROM SOCIO GROUP BY NOMECIRCOLO;", 1);
INSERT INTO Sketch_Codice VALUES(1, 4, "Test Completo", "SELECT NOME, COGNOME FROM SOCIO WHERE CF IN (SELECT CFSOCIO FROM TELEFONO GROUP BY CFSOCIO HAVING COUNT(*) >= 2);", 1);
INSERT INTO Sketch_Codice VALUES(1, 5, "Test Completo", "SELECT CITTA, SUM(NUMEROCAMPITENNIS) FROM CIRCOLO GROUP BY CITTA HAVING SUM(NUMEROCAMPITENNIS) > 5;", 1);
INSERT INTO Sketch_Codice VALUES(1, 6, "Test Completo", "SELECT NOME FROM CIRCOLO WHERE NUMEROCAMPITENNIS > 3 AND NOME NOT IN (SELECT NOMECIRCOLO FROM SOCIO);", 1);
INSERT INTO Sketch_Codice VALUES(1, 7, "Test Completo", "SELECT DISTINCT NOME FROM CIRCOLO WHERE (CITTA = 'BOLOGNA') AND (NOME IN ( SELECT NOMECIRCOLO FROM SOCIO GROUP BY NOMECIRCOLO HAVING COUNT(DISTINCT CF) >= 3));", 1);
INSERT INTO Sketch_Codice VALUES(1, 8, "Test Completo", "SELECT CFSOCIO, COUNT(*) FROM SOCIO, TELEFONO, CIRCOLO WHERE (CFSOCIO = CF) AND (NOMECIRCOLO = CIRCOLO.NOME) AND (CITTA = 'BOLOGNA') GROUP BY CFSOCIO;", 1);

INSERT INTO Test VALUES("Test Parziale", "luigi.bianchi@gmail.com", NULL, "2008-10-29 14:56:59", 0);

INSERT INTO Quesito VALUES(9, "Test Parziale", 'MEDIO', 0, "Seleziona la query corretta");
INSERT INTO Quesito VALUES(10, "Test Parziale", 'BASSO', 0, "Scrivere la query che visualizza il numero di soci nati prima del 2000 (escluso)");
INSERT INTO Quesito VALUES(11, "Test Parziale", 'BASSO', 0, "La query 'SELECT COUNT(*) FROM SOCIO WHERE (ANNONASCITA < 2000);' è corretta?");

INSERT INTO Afferenza VALUES(9, "Test Parziale", 1);
INSERT INTO Afferenza VALUES(9, "Test Parziale", 2);
INSERT INTO Afferenza VALUES(10, "Test Parziale", 2);
INSERT INTO Afferenza VALUES(11, "Test Parziale", 2);


INSERT INTO Domanda_Chiusa VALUES(9, "Test Parziale");
INSERT INTO Domanda_Codice VALUES(10, "Test Parziale");
INSERT INTO Domanda_Chiusa VALUES(11, "Test Parziale");

INSERT INTO Opzione_Risposta VALUES(1, 9, "Test Parziale", "SELECT ALL FROM CIRCOLO;", 0);
INSERT INTO Opzione_Risposta VALUES(2, 9, "Test Parziale", "SELECT * FROM CIRCOLO;", 1);
INSERT INTO Opzione_Risposta VALUES(3, 9, "Test Parziale", "SELECT ALL FROM;", 0);
INSERT INTO Sketch_Codice VALUES(1, 10, "Test Parziale", "SELECT COUNT(*) FROM SOCIO WHERE (ANNONASCITA < 2000);", 1);
INSERT INTO Opzione_Risposta VALUES(1, 11, "Test Parziale", "VERO", 1);
INSERT INTO Opzione_Risposta VALUES(2, 11, "Test Parziale", "FALSO", 0);
