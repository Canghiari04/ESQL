<?php
    function getTypeQuestion($conn, $idQuestion, $titleTest) {
        $sql = "SELECT * FROM Domanda_Chiusa WHERE ((ID_DOMANDA_CHIUSA = :idQuesito) AND (TITOLO_TEST = :titoloTest));";

        try {
            $stmt = $conn -> prepare($sql);
            $stmt -> bindValue(":idQuesito", $idQuestion);          
            $stmt -> bindValue(":titoloTest", $titleTest);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $numRows = $stmt -> rowCount();
        if($numRows > 0) {
            return "CHIUSA";
        } else {
            return "APERTA";
        }
    }

    function addOption($conn, $type, $id, $idQuestion, $titleTest, $textAnswer, $sltSolution) {
        if($type == "CHIUSA") { // diversificazione della procedure a seconda della tipologia del quesito
            $storedProcedure = "CALL Inserimento_Opzione_Risposta(:id, :idQuesito, :titoloTest, :testo, :soluzione);";            
            insertOption($conn, $storedProcedure, $id, $idQuestion, $titleTest, $textAnswer, $sltSolution);
        } else { 
            $storedProcedure = "CALL Inserimento_Sketch_Codice(:id, :idQuesito, :titoloTest, :testo, :soluzione);";
            
            if(checkQuery($conn, $textAnswer)) {
                insertOption($conn, $storedProcedure, $id, $idQuestion, $titleTest, $textAnswer, $sltSolution);
            }
        }
    }

    function getLastId($conn, $type, $idQuestion, $titleTest) { // funzione adeguata per restituire l'ultimo numero progressivo
        if($type == "CHIUSA") { // diversficazione della query a seconda della tipologia
            $sql = "SELECT MAX(ID) AS MAX_ID_ANSWER FROM Opzione_Risposta WHERE ((Opzione_Risposta.ID_DOMANDA_CHIUSA=:idQuesito) AND (Opzione_Risposta.TITOLO_TEST=:titoloTest));";
        } else {    
            $sql = "SELECT MAX(ID) AS MAX_ID_ANSWER FROM Sketch_Codice WHERE ((Sketch_Codice.ID_DOMANDA_CODICE=:idQuesito) AND (Sketch_Codice.TITOLO_TEST=:titoloTest));";
        }

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);
            
            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $row = $result -> fetch(PDO::FETCH_OBJ);
        return ($row -> MAX_ID_ANSWER + 1);
    }

    function insertOption($conn, $storedProcedure, $id, $idQuestion, $titleTest, $textAnswer, $sltSolution) {
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

    function checkQuery($conn, $textAnswer) { // controllo della correttezza sintattica della query
        try {
            $result = $conn -> prepare($textAnswer);
            
            $result-> execute();
        } catch(PDOException $e) {
            echo '<script>alert("Inserire una query valida. \r\r'.$e -> getMessage().'.");</script>';
            return false;
        }
    
        return true;
    }
?>