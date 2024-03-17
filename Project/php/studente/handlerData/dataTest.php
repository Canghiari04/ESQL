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

    /* visualizzazione della checkbox demarcata o meno */
    function printChecked($idSolution, $questionSolutions){
        if(in_array($idSolution, $questionSolutions)){
            return "checked";
        }
        else {
            return "";
        }
    }

    /* individuazione delle opzioni di risposta date in tentavi precedenti, da cui verrà visualizzata o meno la checkbox demarcata */
    function checkChecked($conn, $email, $idQuestion, $titleTest){
        $sql = "SELECT TESTO FROM Risposta WHERE (EMAIL_STUDENTE=:emailStudente) AND (ID_QUESITO=:idQuesito) AND (TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $email);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $questionSolutions = array();

        /* in presenza di un oggetto PDO non nullo, verranno salvati all'interno dell'array gli id delle opzioni di risposta */
        if($result -> rowCount()>0){
            $row = $result -> fetch(PDO::FETCH_OBJ);
            $questionSolutions =  explode('|?|', $row -> TESTO);
        }
        
        return $questionSolutions;
    }

    /* funzione simile a quella precedente, in cui si osserva se lo studente abbia dato qualche risposta in tentavi precedenti */
    function checkAnswered($conn, $email, $idQuestion, $titleTest){
        $sql = "SELECT TESTO FROM Risposta WHERE (EMAIL_STUDENTE=:emailStudente) AND (ID_QUESITO=:idQuesito) AND (TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $email);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        if($result -> rowCount()>0){       
            $row = $result -> fetch(PDO::FETCH_OBJ); 
            echo "<script>document.querySelector('textarea[name=\"txtAnswerSketch".$idQuestion."\"]').value='".$row -> TESTO."';</script>";
        }
    }

    function checkSolution($conn, $idQuestion, $titleTest) {
        $sql = "SELECT TESTO FROM Sketch_Codice WHERE (Sketch_Codice.ID_DOMANDA_CODICE=:idQuesito) AND (Sketch_Codice.TITOLO_TEST=:titoloTest) AND (Sketch_Codice.SOLUZIONE=1);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        if(isset($result)){       
            $row = $result -> fetch(PDO::FETCH_OBJ); 
            echo "<script>document.querySelector('textarea[name=\"txtAnswerSketch".$idQuestion."\"]').value='".$row -> TESTO."';</script>";
        }
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
?>