DROP DATABASE IF EXISTS ESQLDB;
CREATE DATABASE IF NOT EXISTS ESQLDB;

USE ESQLDB;

CREATE TABLE Utente (
	EMAIL VARCHAR(255) PRIMARY KEY,
    PSWD VARCHAR(255),
    NOME VARCHAR(255) NOT NULL,
    COGNOME VARCHAR(255) NOT NULL,
    TELEFONO INT(10)
)ENGINE=INNODB ;

CREATE TABLE Studente (
	EMAIL_STUDENTE VARCHAR(255) PRIMARY KEY,
    ANNO_IMMATRICOLAZIONE INT(4) NOT NULL,
    CODICE VARCHAR(16) NOT NULL,
    FOREIGN KEY (EMAIL_STUDENTE) REFERENCES Utente(EMAIL) ON DELETE CASCADE
)ENGINE=INNODB ;

CREATE TABLE Docente (
	EMAIL_DOCENTE VARCHAR(255) PRIMARY KEY,
    NOME_DIPARTIMENTO VARCHAR(255) NOT NULL,
    NOME_CORSO VARCHAR(255) NOT NULL,
    FOREIGN KEY (EMAIL_DOCENTE) REFERENCES Utente(EMAIL) ON DELETE CASCADE
)ENGINE=INNODB ;

CREATE TABLE Tabella_Esercizio (
	ID INT AUTO_INCREMENT PRIMARY KEY,
    NOME VARCHAR(255) NOT NULL,
    DATA_CREAZIONE DATETIME NOT NULL,
    NUM_RIGHE INT NOT NULL
)ENGINE=INNODB ;

-- IDEA DI COMPOSIZIONE TRA ATTRIBUTO E TABELLA_ESERCIZIO, MAGARI DOMANDA AL TUTOR!

CREATE TABLE Attributo (
	ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_TABELLA INT,
    TIPO VARCHAR(255) NOT NULL,
    NOME VARCHAR(255) NOT NULL,
    CHIAVE_PRIMARIA BOOLEAN NOT NULL,
    FOREIGN KEY(ID_TABELLA) REFERENCES Tabella_Esercizio(ID) ON DELETE CASCADE 
)ENGINE=INNODB ;

CREATE TABLE Vincolo_Integrita (
	REFERENTE INT,
    REFERENZIATO INT,
    PRIMARY KEY (REFERENTE, REFERENZIATO),
    FOREIGN KEY (REFERENTE) REFERENCES Attributo(ID) ON DELETE CASCADE,
    FOREIGN KEY (REFERENZIATO) REFERENCES Attributo(ID) ON DELETE CASCADE
)ENGINE=INNODB ;

CREATE TABLE Test (
	TITOLO VARCHAR(255) PRIMARY KEY,
    EMAIL_DOCENTE VARCHAR(255) NOT NULL,
    FOTO BLOB,
    DATA_CREAZIONE DATE NOT NULL,
    VISUALIZZA_RISPOSTE BOOLEAN NOT NULL,
    FOREIGN KEY (EMAIL_DOCENTE) REFERENCES Docente(EMAIL_DOCENTE)
)ENGINE=INNODB ;

CREATE TABLE Quesito (
	ID INT AUTO AUTO_INCREMENT,
    TITOLO_TEST VARCHAR(255),
    DIFFICOLTA ENUM('BASSO', 'MEDIO', 'ALTO') NOT NULL,
    DESCRIZIONE VARCHAR(255) NOT NULL,
    NUM_RISPOSTE INT NOT NULL,
    PRIMARY KEY(ID, TITOLO_TEST),
    FOREIGN KEY (TITOLO_TEST) REFERENCES Test(TITOLO)
)ENGINE=INNODB ;

CREATE TABLE Afferenza (
	ID_QUESITO INT,
    ID_TABELLA INT,
    TITOLO_TEST VARCHAR(255),
    PRIMARY KEY(ID_QUESITO, ID_TABELLA, TITOLO_TEST),
    FOREIGN KEY(ID_QUESITO) REFERENCES Quesito(ID),
    FOREIGN KEY(ID_TABELLA) REFERENCES Tabella_Esercizio(ID),
    FOREIGN KEY(TITOLO_TEST) REFERENCES Quesito(TITOLO_TEST)
)ENGINE=INNODB ;

CREATE TABLE Domanda_Chiusa (
	ID_DOMANDA_CHIUSA INT PRIMARY KEY,
    FOREIGN KEY(ID_DOMANDA_CHIUSA) REFERENCES Quesito(ID) ON DELETE CASCADE  
)ENGINE=INNODB ;

CREATE TABLE Opzione_Risposta (
	ID INT,
    ID_DOMANDA_CHIUSA INT,
    TESTO VARCHAR(255) NOT NULL,
    PRIMARY KEY(ID, ID_DOMANDA_CHIUSA),
    FOREIGN KEY(ID_DOMANDA_CHIUSA) REFERENCES Domanda_Chiusa(ID_DOMANDA_CHIUSA) ON DELETE CASCADE
)ENGINE=INNODB ;

CREATE TABLE Domanda_Codice (
	ID_DOMANDA_CODICE INT PRIMARY KEY,
    FOREIGN KEY(ID_DOMANDA_CODICE) REFERENCES Quesito(ID) ON DELETE CASCADE    
)ENGINE=INNODB ;

CREATE TABLE Sketch_Codice (
	ID INT,
    ID_DOMANDA_CODICE INT,
    TESTO VARCHAR(255) NOT NULL,
    PRIMARY KEY(ID, ID_DOMANDA_CODICE),
    FOREIGN KEY(ID_DOMANDA_CODICE) REFERENCES Domanda_Codice(ID_DOMANDA_CODICE) ON DELETE CASCADE
)ENGINE=INNODB ;

CREATE TABLE Completamento (
	TITOLO_TEST VARCHAR(255), 
    EMAIL_STUDENTE VARCHAR(255),
    STATO ENUM('APERTO', 'INCOMPLETAMENTO', 'CONCLUSO') NOT NULL, 
    DATA_ULTIMARISPOSTA DATETIME NOT NULL,
    DATA_PRIMARISPOSTA DATETIME NOT NULL,
    PRIMARY KEY(TITOLO_TEST, EMAIL_STUDENTE),
    FOREIGN KEY(TITOLO_TEST) REFERENCES Test(TITOLO),
    FOREIGN KEY(EMAIL_STUDENTE) REFERENCES Studente(EMAIL_STUDENTE)
)ENGINE=INNODB ;

CREATE TABLE Risposta (
	EMAIL_STUDENTE VARCHAR(255),
    ID_QUESITO INT,
    TESTO VARCHAR(255) NOT NULL,
    ESITO BOOLEAN NOT NULL,
    PRIMARY KEY(EMAIL_STUDENTE, ID_QUESITO),
    FOREIGN KEY(ID_QUESITO) REFERENCES Quesito(ID),
    FOREIGN KEY(EMAIL_STUDENTE) REFERENCES Studente(EMAIL_STUDENTE)    
)ENGINE=INNODB ;

CREATE TABLE Messaggio (
	ID INT AUTO_INCREMENT PRIMARY KEY,
    EMAIL_DOCENTE VARCHAR(255) NOT NULL,
    TESTO VARCHAR(255) NOT NULL,
    TITOLO VARCHAR(255) NOT NULL,
    DATA_INSERIMENTO DATE NOT NULL,
    TITOLO_TEST VARCHAR(255) NOT NULL,
    FOREIGN KEY(TITOLO_TEST) REFERENCES Test(TITOLO),
    FOREIGN KEY(EMAIL_DOCENTE) REFERENCES Docente(EMAIL_DOCENTE)
)ENGINE=INNODB ;

CREATE TABLE Messaggio_Studente (
	ID_MESSAGGIO_STUDENTE INT PRIMARY KEY,
    EMAIL_STUDENTE VARCHAR(255) NOT NULL,
    FOREIGN KEY(ID_MESSAGGIO_STUDENTE) REFERENCES Messaggio(ID),
    FOREIGN KEY(EMAIL_STUDENTE) REFERENCES Studente(EMAIL_STUDENTE)
)ENGINE=INNODB ;