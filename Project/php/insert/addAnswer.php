<?php
    /* inserimento delle risposte alla domanda di riferimento, adeguando la procedure corretta, in base alla tipologia della stessa */
    function addAnswer($conn, $type, $idQuestion, $textAnswer) {
        if($type == 'CHIUSA') {
            $storedProcedure = 'CALL Inserimento_Opzione_Risposta(:idQuesito, :testo);';
        } else {   
            $storedProcedure = 'CALL Inserimento_Sketch_Codice(:idQuesito, :testo);';
        }

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(':idQuesito', $idQuestion);
            $stmt -> bindValue(':testo', $textAnswer);
            
            $stmt -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }
    }

    function checkNumAnswer($conn, $type, $idQuestion) {
        /* metodo che fornisce il numero di risposte atteso, per conseguente controllo di risposte effettivamente presenti */
        $numAnswers = getNumberAnswers($conn, $idQuestion);

        if($type == 'CHIUSA') {
            $sql = 'SELECT COUNT(*) AS numInsertedAnswers FROM Opzione_Risposta WHERE (ID_DOMANDA_CHIUSA=:id);';
        } else {
            $sql = 'SELECT COUNT(*) AS numInsertedAnswers FROM Sketch_Codice WHERE (ID_DOMANDA_CODICE=:id);';
        }

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(':id', $idQuestion);

            $result -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        /* dalle righe sottostanti è possibile risalire al numero di risposte effettive connesse alla domanda di riferimento */
        $row = $result -> fetch(PDO::FETCH_ASSOC);
        $numInsertedAnswers = $row['numInsertedAnswers'];

        return ($numAnswers == $numInsertedAnswers);
    }

    function getNumberAnswers($conn, $idQuestion) {
        $sql = "SELECT * FROM Quesito WHERE (ID=:id);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":id", $idQuestion);

            $result -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        $row = $result -> fetch(PDO::FETCH_ASSOC);
        $numAnswers = $row["NUM_RISPOSTE"];

        return $numAnswers;
    }
?>