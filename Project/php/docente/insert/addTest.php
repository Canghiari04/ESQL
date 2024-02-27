<?php
    function checkQuestion($conn) {
        $sql = 'SELECT * FROM Quesito;';

        try {
            $result = $conn -> prepare($sql);
            
            $result -> execute();
            $numRows = $result -> rowCount();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        return ($numRows > 0);
    }

    function insertTest($conn, $email, $viewAnswers, $fileTest, $titleTest) {
        $storedProcedure = 'CALL Inserimento_Test(:titolo, :email, :foto, :dataCreazione, :visualizzaRisposte);';

        $viewAnswers = convertToBoolean($viewAnswers);

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(':titolo', $titleTest);
            $stmt -> bindValue(':email', $email);
            $stmt -> bindValue(':foto', $fileTest);
            $stmt -> bindValue(':dataCreazione', date('Y-m-d'));
            $stmt -> bindValue(':visualizzaRisposte', $viewAnswers);
            
            $stmt -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }
    }

    function convertToBoolean($viewAnswers) {
        if ($viewAnswers == 'false') {
            return 0;
        } else {
            return 1;
        }
    }
?>