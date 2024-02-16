<?php
    session_start();

    function insertQuestion($conn, $type, $difficulty, $numAnswers, $description) {
        $storedProcedure = "CALL Inserimento_Quesito(:difficolta, :numRisposte, :descrizione)";

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":difficolta", $difficulty);
            $stmt -> bindValue(":numRisposte", $numAnswers);
            $stmt -> bindValue(":descrizione", $description);
            
            $stmt -> execute();
        } catch (PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        /* query che permette di risalire all'ID dell'ultimo record inserito */
        $sql = "SELECT LAST_INSERT_ID() AS ID";

        try {
            $result = $conn -> prepare($sql);
            $result -> execute();
            
            $row = $result -> fetch(PDO::FETCH_ASSOC);
            $id = $row['ID'];
        } catch (PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        $_SESSION["idCurrentQuestion"] = $id;
        addDomanda($conn, strtoupper($type), $id);
    }

    function addDomanda($conn, $type, $id) {
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
    }
?>