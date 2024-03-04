<?php
    function getTypeQuestion($conn, $idQuestion, $titleTest) {
        $sql = "SELECT * FROM Domanda_Chiusa WHERE ((ID_DOMANDA_CHIUSA = :idQuesito) AND (TITOLO_TEST = :titoloTest));";

        try {
            $stmt = $conn -> prepare($sql);
            $stmt -> bindValue(":idQuesito", $idQuestion);          
            $stmt -> bindValue(":titoloTest", $titleTest);

            $stmt -> execute();
            $numRows = $stmt -> rowCount();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        if($numRows > 0) {
            return "CHIUSA";
        } else {
            return "APERTA";
        }
    }

    /* inserimento delle risposte alla domanda di riferimento, adeguando la procedure corretta, in base alla tipologia della stessa */
    function addOption($conn, $type, $id, $idQuestion, $titleTest, $textAnswer, $sltSolution) {
        if($type == "CHIUSA") {
            $storedProcedure = "CALL Inserimento_Opzione_Risposta(:id, :idQuesito, :titoloTest, :testo, :soluzione);";
        } else {   
            $storedProcedure = "CALL Inserimento_Sketch_Codice(:id, :idQuesito, :titoloTest, :testo, :soluzione);";
        }

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":id", $id);
            $stmt -> bindValue(":idQuesito", $idQuestion);
            $stmt -> bindValue(":titoloTest", $titleTest);
            $stmt -> bindValue(":testo", $textAnswer);
            $stmt -> bindValue(":soluzione", $sltSolution);
            
            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }

    function getLastId($conn, $type, $idQuestion, $titleTest) {
        if($type == "CHIUSA") {    
            $sql = "SELECT MAX(ID) AS MAX_ID_ANSWER FROM Opzione_Risposta WHERE ((Opzione_Risposta.ID_DOMANDA_CHIUSA=:idQuesito) AND (Opzione_Risposta.TITOLO_TEST=:titoloTest));";
        } else {    
            $sql = "SELECT MAX(ID) AS MAX_ID_ANSWER FROM Sketch_Codice WHERE ((Sketch_Codice.ID_DOMANDA_CODICE=:idQuesito) AND (Sketch_Codice.TITOLO_TEST=:titoloTest));";
        }

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);
            
            $result -> execute();
            $numRows = $result -> rowCount();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $row = $result -> fetch(PDO::FETCH_OBJ);
        return ($row -> MAX_ID_ANSWER + 1);
    }
?>