<?php
    /* metodo che restituisce tutti gli id dei quesiti che compongono il test selezionato */
    function getQuestionTest($conn, $titleTest) {
        $sql = "SELECT Quesito.ID FROM Test, Quesito WHERE (Test.TITOLO=Quesito.TITOLO_TEST) AND (Test.TITOLO=:titoloTest);";           

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>"; 
        }

        return $result;
    }

    /* funzione che distringua la tipologia di quesito, da cui scaturisca la stampa differente del form di risposta alla domanda proposta */
    function getTypeQuestion($conn, $idQuestion, $titleTest) {
        $sql = "SELECT * FROM Quesito, Domanda_Chiusa WHERE (Quesito.ID=Domanda_Chiusa.ID_DOMANDA_CHIUSA) AND (Quesito.TITOLO_TEST=Domanda_Chiusa.TITOLO_TEST) AND (Quesito.TITOLO_TEST=:titoloTest) AND (Quesito.ID = :idQuesito);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $numRows = $result -> rowCount();

        if($numRows > 0) {
            return "CHIUSA";
        } else {
            return "CODICE";
        }
    }

    function getQuestionDescription($conn, $idQuestion, $titleTest) {
        $sql = "SELECT DESCRIZIONE FROM Quesito WHERE (ID=:idQuesito) AND (TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(':idQuesito', $idQuestion);
            $result -> bindValue(':titoloTest', $titleTest);

            $result -> execute();
        } catch (PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>'; 
        }

        $row = $result -> fetch(PDO::FETCH_OBJ);

        return $row -> DESCRIZIONE;
    }

    function checkChecked($conn, $emailStudent, $idQuestion, $titleTest){
        $sql = "SELECT TESTO FROM Risposta WHERE (EMAIL_STUDENTE=:emailStudente) AND (ID_QUESITO=:idQuesito) AND (TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $emailStudent);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
            $numRows = $result -> rowCount();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $questionSolutions = array();

        if($numRows > 0){
            $row = $result -> fetch(PDO::FETCH_OBJ);
            $questionSolutions =  explode('|?|', $row -> TESTO);
        }
        
        return $questionSolutions;
    }

    function printChecked($idSolution, $questionSolutions){
        if(in_array($idSolution, $questionSolutions)){
            return "checked";
        }
        else {
            return "";
        }
    }

    function checkAnswered($conn, $emailStudent, $idQuestion, $titleTest){
        $sql = "SELECT TESTO FROM Risposta WHERE (EMAIL_STUDENTE=:emailStudente) AND (ID_QUESITO=:idQuesito) AND (TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $emailStudent);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
            $numRows = $result -> rowCount();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        if($numRows > 0){       
            $row = $result -> fetch(PDO::FETCH_OBJ); 
            echo "<script>document.querySelector('textarea[name=\"txtAnswerSketch" . $idQuestion . "\"]').value='" . $row->TESTO . "';</script>";
        }
    }

    function getNameTable($conn, $idQuestion, $titleTest) {
        $arrayNameTable = array();

        $sql = "SELECT ID_TABELLA FROM Afferenza WHERE (ID_QUESITO=:idQuesito) AND (TITOLO_TEST=:titoloTest);";

        try {
            $resultIdTable = $conn -> prepare($sql);
            $resultIdTable -> bindValue(":idQuesito", $idQuestion);
            $resultIdTable -> bindValue(":titoloTest", $titleTest);            
            
            $resultIdTable -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        } 

        while($row = $resultIdTable -> fetch(PDO::FETCH_OBJ)) {
            /* ottengo prima le chiavi primarie di tutte le tabelle esistenti, contenute all'interno della collezione Tabella_Esercizio */
            $idTable = $row -> ID_TABELLA;

            $sql = "SELECT NOME FROM Tabella_Esercizio WHERE (ID=:idTabella);";

            try {
                $resultNameTable = $conn -> prepare($sql);
                $resultNameTable -> bindValue(":idTabella", $idTable);

                $resultNameTable -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            $row = $resultNameTable -> fetch(PDO::FETCH_OBJ);
            array_push($arrayNameTable, $row -> NOME);
        }
        
        return $arrayNameTable;
    }

    /* restituzione dei field che compongano la tabella circoscritta */
    function getHeaderTable($conn, $nameTable) {
        $sql = "SHOW COLUMNS FROM ".$nameTable.";";

        try {
            $result = $conn -> prepare($sql);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $rows = $result -> fetchAll(PDO::FETCH_ASSOC); 
        return $rows;
    }

    /* metodo che acquisisce tutti i record che caratterizzano la collezione */
    function getContentTable($conn, $nameTable) {
        $sql = "SELECT * FROM ".$nameTable.";";

        try {
            $result = $conn -> prepare($sql);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $rows = $result -> fetchAll(PDO::FETCH_ASSOC); 
        return $rows;
    }
?>