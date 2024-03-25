<?php
    function insertTest($conn, $email, $viewAnswers, $uploadFile, $titleTest) {
        if($uploadFile != null) {
            $fileTest = file_get_contents($uploadFile);
        } else {
            $fileTest = null;
        }

        $viewAnswers = convertToBoolean($viewAnswers);

        $storedProcedure = "CALL Inserimento_Test(:titolo, :email, :foto, :dataCreazione, :visualizzaRisposte);";

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":titolo", $titleTest);
            $stmt -> bindValue(":email", $email);
            $stmt -> bindValue(":foto", $fileTest);
            $stmt -> bindValue(":dataCreazione", date("Y-m-d"));
            $stmt -> bindValue(":visualizzaRisposte", $viewAnswers);
            
            $stmt -> execute();
        } catch(PDOException $e) {
            echo '<script>alert("Si è verificato un errore. \r\rRitenta.");</script>';
        }
    }

    function convertToBoolean($viewAnswers) {
        if ($viewAnswers == "false") {
            return 0;
        } else {
            return 1;
        }
    }
?>