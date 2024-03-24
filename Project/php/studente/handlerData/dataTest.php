<?php   
    /* metodo che restituisce tutti gli id dei quesiti che compongono il test selezionato */
    function getQuestionTest($conn, $titleTest) {
        $arrayIdQuestion = array();
        $sql = "SELECT Quesito.ID FROM Quesito WHERE (Quesito.TITOLO_TEST=:titoloTest);";       

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>"; 
        }

        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
            array_push($arrayIdQuestion, $row -> ID);
        }

        return $arrayIdQuestion;
    }

    /* funzione restituente l'insieme delle risposte date dallo studente rispetto allo specifico test */
    function getAnswerTest($conn, $email, $titleTest) {
        $sql = "SELECT * FROM Risposta WHERE (Risposta.EMAIL_STUDENTE=:emailStudente) AND (Risposta.TITOLO_TEST=:titoloTest);";
                    
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $email);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        return $result;
    }

    /* funzione che distingue la tipologia di quesito, da cui scaturisce la stampa del form di risposta alla domanda proposta */
    function getTypeQuestion($conn, $idQuestion, $titleTest) {
        $sql = "SELECT * FROM Domanda_Chiusa WHERE (Domanda_Chiusa.ID_DOMANDA_CHIUSA=:idQuesito) AND (Domanda_Chiusa.TITOLO_TEST=:titoloTest);";

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

    /* metodo necessario per acquisire la descrizione del quesito */
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

    function getIdTable($conn, $nameTable) {
        $sql = "SELECT ID FROM Tabella_Esercizio WHERE (Tabella_Esercizio.NOME=:nomeTabella);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":nomeTabella", $nameTable);

            $result -> execute();
        } catch (PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>'; 
        }

        $row = $result -> fetch(PDO::FETCH_OBJ);
        return $row -> ID;
    }

    /* acquisizione di tutte le tabelle che abbiano un'afferenza rispetto al quesito visualizzato */
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
            /* dagli id delle tabelle sono ricondotti i nomi di ognuna di esse*/
            $idTable = $row -> ID_TABELLA;

            $sql = "SELECT NOME FROM Tabella_Esercizio WHERE (ID=:idTabella);";

            try {
                $resultNameTable = $conn -> prepare($sql);
                $resultNameTable -> bindValue(":idTabella", $idTable);

                $resultNameTable -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            /* inserimento all'interno di un vettore di tutti i nomi delle tabelle che abbiano un'afferenza con il quesito di riferimento */
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

    function getFieldName($resultAnswer, $resultSolution) {
        $fieldNameAnswer = array();
        $fieldNameResult = array();

        for ($i = 0; $i < ($resultAnswer -> columnCount()); $i++) {
            $field = $resultAnswer -> getColumnMeta($i);
            array_push($fieldNameAnswer, $field["name"]);
        }

        for ($i = 0; $i < ($resultSolution -> columnCount()); $i++) {
            $field = $resultSolution -> getColumnMeta($i);
            array_push($fieldNameResult, $field["name"]);
        }

        return [$fieldNameAnswer, $fieldNameResult];
    }
?>