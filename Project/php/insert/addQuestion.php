<?php
    function insertRecord($conn, $type, $difficulty, $description, $numAnswers, $nameTable) {
        $storedProcedure = "CALL Inserimento_Quesito(:difficolta, :descrizione, :numRisposte)";

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":difficolta", $difficulty);
            $stmt -> bindValue(":descrizione", $description);
            $stmt -> bindValue(":numRisposte", $numAnswers);
            
            $stmt -> execute();
        } catch (PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        $sql = "SELECT LAST_INSERT_ID() AS ID";

        try {
            $result = $conn -> prepare($sql);
            $result -> execute();
            
            $row = $result -> fetch(PDO::FETCH_ASSOC);
            $id = $row['ID'];
        } catch (PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        addDomanda($conn, strtoupper($type), $id, $nameTable);
    }

    function addDomanda($conn, $type, $id, $nameTable) {
        if($type == "CHIUSA") {
            $storedProcedure = "CALL Inserimento_Domanda_Chiusa(:id);";
        } elseif($type == "CODICE") {
            $storedProcedure = "CALL Inserimento_Domanda_Codice(:id);";
        }

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":id", $id);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        $storedProcedure = "CALL Inserimento_Afferenza(:id, :nomeTabella);";

        /* STORED PROCEDURE PER LA CREAZIONE DI AFFERENZE */
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":id", $id);
            $stmt -> bindValue(":id", $nameTable);

            //$stmt -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }
    }
?>