DROP DATABASE IF EXISTS ESQL;
CREATE DATABASE IF NOT EXISTS ESQL;

USE ESQL;

CREATE TABLE Utente 
(
	EMAIL VARCHAR(255) PRIMARY KEY,
    NOME VARCHAR(255) NOT NULL,
    COGNOME VARCHAR(255) NOT NULL,
    TELEFONO INT(10)
)ENGINE=INNODB;

CREATE TABLE Studente (
	EMAIL_STUDENTE VARCHAR(255) PRIMARY KEY,
    ANNO_IMMATRICOLAZIONE INT(4) NOT NULL,
    CODICE NVARCHAR(16) NOT NULL,
    FOREIGN KEY (EMAIL_STUDENTE) REFERENCES Utente(EMAIL)
)ENGINE=INNODB;

CREATE TABLE Docente (
	EMAIL_DOCENTE VARCHAR(255) PRIMARY KEY,
    NOME_DIPARTIMENTO VARCHAR(255) NOT NULL,
    NOME_CORSO VARCHAR(255) NOT NULL,
    FOREIGN KEY (EMAIL_DOCENTE) REFERENCES Utente(EMAIL)
)ENGINE=INNODB;

CREATE TABLE Tabelle_Esercizio (
	ID_TABELLA INT AUTO_INCREMENT PRIMARY KEY,
    NOME VARCHAR(255) NOT NULL,
    DATA_CREAZIONE DATETIME NOT NULL,
    NUM_RIGHE INT NOT NULL
)ENGINE=INNODB;

CREATE TABLE Attributo (
	ID_ATTRIBUTO INT AUTO_INCREMENT PRIMARY KEY,
    ID_TABELLA_ESERCIZIO INT NOT NULL,
    TIPO VARCHAR(255) NOT NULL,
    NOME VARCHAR(255) NOT NULL,
    CHIAVE_PRIMARIA BOOLEAN NOT NULL,
    FOREIGN KEY (ID_TABELLA_ESERCIZIO) REFERENCES Tabelle_Esercizio(ID_TABELLA)
)ENGINE=INNODB;

CREATE TABLE Vincoli_Integrita (
	REFERENTE INT,
    REFERENZA INT,
    PRIMARY KEY (REFERENTE, REFERENZA),
    FOREIGN KEY (REFERENTE) REFERENCES Attributo(ID_ATTRIBUTO),
    FOREIGN KEY (REFERENZA) REFERENCES Attributo(ID_ATTRIBUTO)
)ENGINE=INNODB;

CREATE TABLE Test (
	TITOLO VARCHAR(255) PRIMARY KEY,
    EMAIL_DOCENTI VARCHAR(255) NOT NULL,
    FOTO BLOB,
    DATA_CREAZIONE DATE NOT NULL,
    VISUALIZZA_RISPOSTE BOOLEAN NOT NULL,
    FOREIGN KEY (EMAIL_DOCENTI) REFERENCES Docente(EMAIL_DOCENTE)
)ENGINE=INNODB;

CREATE TABLE Quesito (
	ID_QUESITO INT,
    TITOLO_TEST VARCHAR(255),
    DIFFICOLTA ENUM('BASSO', 'MEDIO', 'ALTO') NOT NULL,
    DESCRIZIONE VARCHAR(255) NOT NULL,
    NUM_RISPOSTE INT NOT NULL,
    PRIMARY KEY(ID_QUESITO, TITOLO_TEST),
    FOREIGN KEY (TITOLO_TEST) REFERENCES Test(TITOLO)
)ENGINE=INNODB;

CREATE TABLE Afferenza (
	ID_QUESITO INT,
    ID_TABELLA INT,
    TITOLO_TEST VARCHAR(255),
    PRIMARY KEY(ID_QUESITO, ID_TABELLA, TITOLO_TEST),
    FOREIGN KEY(ID_QUESITO) REFERENCES Quesito(ID_QUESITO),
    FOREIGN KEY(ID_TABELLA) REFERENCES Tabelle_Esercizio(ID_TABELLA),
    FOREIGN KEY(TITOLO_TEST) REFERENCES Quesito(TITOLO_TEST)
)ENGINE=INNODB;

CREATE TABLE Domanda_Chiusa (
	ID_DOMANDA_CHIUSA INT PRIMARY KEY,
    FOREIGN KEY(ID_DOMANDA_CHIUSA) REFERENCES Quesito(ID_QUESITO)    
)ENGINE=INNODB;

CREATE TABLE Opzioni_Risposta (
	ID_OPZIONE INT,
    ID_DOMANDA_CHIUSA INT,
    TESTO VARCHAR(255) NOT NULL,
    PRIMARY KEY(ID_OPZIONE, ID_DOMANDA_CHIUSA),
    FOREIGN KEY(ID_DOMANDA_CHIUSA) REFERENCES Domanda_Chiusa(ID_DOMANDA_CHIUSA)
)ENGINE=INNODB;

CREATE TABLE Domanda_Codice (
	ID_DOMANDA_CODICE INT PRIMARY KEY,
    FOREIGN KEY(ID_DOMANDA_CODICE) REFERENCES Quesito(ID_QUESITO)    
)ENGINE=INNODB;

CREATE TABLE Sketch_Codice (
	ID_SKETCH INT,
    ID_DOMANDA_CODICE INT,
    TESTO VARCHAR(255) NOT NULL,
    PRIMARY KEY(ID_SKETCH, ID_DOMANDA_CODICE),
    FOREIGN KEY(ID_DOMANDA_CODICE) REFERENCES Domanda_Codice(ID_DOMANDA_CODICE)
)ENGINE=INNODB;

CREATE TABLE Completamento (
	TITOLO_TEST VARCHAR(255), 
    EMAIL_STUDENTE VARCHAR(255),
    STATO ENUM('APERTO', 'INCOMPLETAMENTO', 'CONCLUSO') NOT NULL, 
    DATA_ULTIMARISPOSTA DATETIME NOT NULL,
    DATA_PRIMARISPOSTA DATETIME NOT NULL,
    PRIMARY KEY(TITOLO_TEST, EMAIL_STUDENTE),
    FOREIGN KEY(TITOLO_TEST) REFERENCES Test(TITOLO),
    FOREIGN KEY(EMAIL_STUDENTE) REFERENCES Studente(EMAIL_STUDENTE)
)ENGINE=INNODB;

CREATE TABLE Risposta (
	EMAIL_STUDENTE VARCHAR(255),
    ID_QUESITO INT,
    TESTO VARCHAR(255) NOT NULL,
    ESITO BOOLEAN NOT NULL,
    PRIMARY KEY(EMAIL_STUDENTE, ID_QUESITO),
    FOREIGN KEY(ID_QUESITO) REFERENCES Quesito(ID_QUESITO),
    FOREIGN KEY(EMAIL_STUDENTE) REFERENCES Studente(EMAIL_STUDENTE)    
)ENGINE=INNODB;

CREATE TABLE Messaggio (
	ID INT AUTO_INCREMENT PRIMARY KEY,
    EMAIL_DOCENTE VARCHAR(255) NOT NULL,
    TITOLO VARCHAR(255) NOT NULL,
    TESTO VARCHAR(255) NOT NULL,
    DATA_INSERIMENTO DATE NOT NULL,
    TITOLO_TEST VARCHAR(255) NOT NULL,
    FOREIGN KEY(TITOLO_TEST) REFERENCES Test(TITOLO),
    FOREIGN KEY(EMAIL_DOCENTE) REFERENCES Docente(EMAIL_DOCENTE)
)ENGINE=INNODB;

CREATE TABLE Messaggio_Studente (
	ID_MESSAGGIO_STUDENTE INT PRIMARY KEY,
    EMAIL_STUDENTE VARCHAR(255) NOT NULL,
    FOREIGN KEY(ID_MESSAGGIO_STUDENTE) REFERENCES Messaggio(ID),
    FOREIGN KEY(EMAIL_STUDENTE) REFERENCES Studente(EMAIL_STUDENTE)
)ENGINE=INNODB;
