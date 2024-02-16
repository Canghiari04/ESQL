<?php
    function checkNumAnswer($conn, $type) {
        $numAnswers = getNumberAnswers($conn);

        if($type = "CHIUSA") {
            $sql = "SELECT COUNT(*) AS numInsertedAnswers FROM Opzione_Risposta WHERE (ID_DOMANDA_CHIUSA=:id);";
        } else {
            $sql = "SELECT COUNT(*) AS numInsertedAnswers FROM Opzione_Risposta WHERE (ID_DOMANDA_CODICE=:id);";
        }

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":id", $_SESSION["idCurrentQuestion"]);

            $result -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        $row = $result -> fetch(PDO::FETCH_ASSOC);
        $numInsertedAnswers = $row["numInsertedAnswers"];

        return ($numAnswers == $numInsertedAnswers);
    }

    function getNumberAnswers($conn) {
        $sql = "SELECT * FROM Quesito WHERE (ID=:id);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":id", $_SESSION["idCurrentQuestion"]);

            $result -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        $row = $result -> fetch(PDO::FETCH_ASSOC);
        $numAnswers = $row["NUM_RISPOSTE"];

        return $numAnswers;
    }

    function addAnswer($conn, $textAnswer) {
        
    }
?>