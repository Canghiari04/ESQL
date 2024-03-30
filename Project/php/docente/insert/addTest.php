<?php
    function insertTest($conn, $manager, $email, $viewAnswers, $uploadFile, $titleTest) { // inserimento di un nuovo test all'interno della tabella Test
        if($uploadFile != null) { // controllo della presenza o meno di un file multimediale
            $fileTest = file_get_contents($uploadFile);
        } else {
            $fileTest = null;
        }

        $viewAnswers = convertToBoolean($viewAnswers); // conversione del booleano in value accettabili da mysql

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
  
        $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento Test titolo: '.$titleTest.'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document); // scrittura log inserimento di un test
    }

    function convertToBoolean($viewAnswers) {
        if($viewAnswers == "false") {
            return 0;
        } else {
            return 1;
        }
    }
?>