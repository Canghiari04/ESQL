<?php
    function checkTable($conn, $email) { // controllo che sia presente almeno una tabella prima dell'inserimento di un quesito in un test
        $sql = "SELECT * FROM Tabella_Esercizio WHERE (EMAIL_DOCENTE=:email);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":email", $email);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $numRows = $result -> rowCount();
        return ($numRows > 0);
    } 

    function checkNumberRows($conn, $email) { // controllo attuato per accertarsi che le tabelle abbiano almeno un record al loro interno
        $sql = "SELECT * FROM Tabella_Esercizio WHERE (Tabella_Esercizio.EMAIL_DOCENTE=:email) AND (Tabella_Esercizio.NUM_RIGHE>0);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":email", $email);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $numRows = $result -> rowCount();
        return ($numRows > 0);
    }

    function getLastId($conn, $titleTest) { // funzione attuata per restituire l'ultimo numero progressivo dalla tabella Quesito
        $sql = "SELECT MAX(ID) AS MAX_ID_QUESTION FROM Quesito WHERE (Quesito.TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":titoloTest", $titleTest);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $row = $result -> fetch(PDO::FETCH_OBJ);
        return ($row -> MAX_ID_QUESTION + 1);
    }

    function insertQuestion($conn, $type, $idQuestion, $titleTest, $difficulty, $numAnswers, $description) {
        $storedProcedure = "CALL Inserimento_Quesito(:idQuesito, :titoloTest, :difficolta, :numRisposte, :descrizione);";

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":idQuesito", $idQuestion);
            $stmt -> bindValue(":titoloTest", $titleTest);
            $stmt -> bindValue(":difficolta", $difficulty);
            $stmt -> bindValue(":numRisposte", $numAnswers);
            $stmt -> bindValue(":descrizione", $description);
            
            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $_SESSION["idCurrentQuestion"] = $idQuestion; // inizializzazione del campo della sessione affinchè sia possibile risalire all'id del quesito per successivo inserimento all'interno della tabella Afferenza
        
        addQuestion($conn, strtoupper($type), $idQuestion, $titleTest);
    }

    function addQuestion($conn, $type, $id, $titleTest) {
        if($type == "CHIUSA") { // diversificazione della procedure a seconda della tipologia
            $storedProcedure = "CALL Inserimento_Domanda_Chiusa(:id, :titoloTesto);";
        } elseif($type == "CODICE") {
            $storedProcedure = "CALL Inserimento_Domanda_Codice(:id, :titoloTesto);";
        }

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":id", $id);
            $stmt -> bindValue(":titoloTesto", $titleTest);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }
?>