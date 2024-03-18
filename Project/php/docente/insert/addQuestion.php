<?php
    function checkTable($conn, $email) {
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

    function getLastId($conn, $titleTest) {
        $sql = "SELECT MAX(ID) AS MAX_ID_QUESTION FROM Quesito WHERE (Quesito.TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":titoloTest", $titleTest);
            
            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $numRows = $result -> rowCount();

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
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* viene salvato l'id dell'ultima domanda inserita, dato che conseguentemente dovranno essere inseriti riferimenti delle risposte e delle tabelle */
        $_SESSION["idCurrentQuestion"] = $idQuestion;
        addQuestion($conn, strtoupper($type), $idQuestion, $titleTest);
    }

    /* funzione utilizzata per smistare l'inserimento della domanda a seconda della tipologia */
    function addQuestion($conn, $type, $id, $titleTest) {
        if($type == "CHIUSA") {
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