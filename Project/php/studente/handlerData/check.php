<?php
    /* funzione attuata per osservare se il test possiede quesiti che abbiano almeno una soluzione */
    function checkCompletedTest($conn, $titleTest) {
        /* array contenente tutti gli id dei quesiti del test */
        $arrayIdQuestion = getQuestionTest($conn, $titleTest);

        /* nel caso in cui il test non abbia quesiti sarà restituito false */
        if(sizeof($arrayIdQuestion) > 0) {    
            for($i = 0; $i <= sizeof($arrayIdQuestion) - 1; $i++) {
                $type = getTypeQuestion($conn, $arrayIdQuestion[$i], $titleTest);
                
                if($type == "CHIUSA") {
                    $sql = "SELECT ID FROM Opzione_Risposta WHERE (Opzione_Risposta.ID_DOMANDA_CHIUSA=:idQuesito) AND (Opzione_Risposta.TITOLO_TEST=:titoloTest) AND (SOLUZIONE=1);";
                } else {
                    $sql = "SELECT TESTO FROM Sketch_Codice WHERE (Sketch_Codice.ID_DOMANDA_CODICE=:idQuesito) AND (Sketch_Codice.TITOLO_TEST=:titoloTest) AND (SOLUZIONE=1);";
                }
                
                try { 
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":idQuesito", $arrayIdQuestion[$i]);
                    $result -> bindValue(":titoloTest", $titleTest);
                    
                    $result -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
                
                /* controllo attuato per osservare se i quesiti del test abbiano almeno una soluzione */
                $numRows = $result -> rowCount();
                if($numRows == 0) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /* funzione che restituisce lo stato attuale di completamento del Test per lo specifico studente */
    function checkStateTest($conn, $email, $titleTest) {
        $sql = "SELECT STATO FROM Completamento WHERE (Completamento.EMAIL_STUDENTE=:emailStudente) AND (Completamento.TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $email);
            $result -> bindValue(":titoloTest", $titleTest);
                        
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";  
        }
    
        $numRows = $result -> rowCount();
        if($numRows > 0) {
            $row = $result -> fetch(PDO::FETCH_OBJ);
            return $row -> STATO;
        } else {
            return " ";
        }
    }

    /* metodo utilizzato per stabilire se sia possibile visualizzare le soluzioni del test */
    function checkViewAnswer($conn, $titleTest) {           
        $sql = "SELECT VISUALIZZA_RISPOSTE FROM Test WHERE (Test.TITOLO=:titoloTest);";
    
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":titoloTest", $titleTest);
    
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    
        $row = $result -> fetch(PDO::FETCH_OBJ);

        if(($row -> VISUALIZZA_RISPOSTE) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /* acquisizione del numero totale di risposte date da uno studente per uno specifico test */
    function checkNumAnswer($conn, $email, $titleTest) {
        $sql = "SELECT * FROM Risposta WHERE (Risposta.EMAIL_STUDENTE=:emailStudente) AND (Risposta.TITOLO_TEST=:titoloTest);";           
    
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $email);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>"; 
        }
                    
        $numRows = $result -> rowCount();
        return ($numRows > 0);
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
        $numRows = $result -> rowCount();
        if($numRows > 0){
            $row = $result -> fetch(PDO::FETCH_OBJ);
            $questionSolutions =  explode('|?|', $row -> TESTO);
        }
        
        return $questionSolutions;
    }

    /* metodo attuato per stampare all'interno della textarea la risposta data dallo studente */
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

        /* controllo per definire se lo studente abbia o meno risposto al quesito */
        $numRows = $result -> rowCount();
        if($numRows > 0){      
            $row = $result -> fetch(PDO::FETCH_OBJ); 
            echo "<script>document.querySelector('textarea[name=\"txtAnswerSketch".$idQuestion."\"]').value='".$row -> TESTO."';</script>";
        }
    }

    /* funzione definita per stampare all'interno delle textarea le soluzioni di ogni Domanda_Codice */
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

        $numRows = $result -> rowCount();
        if($numRows > 0){       
            $row = $result -> fetch(PDO::FETCH_OBJ); 
            echo "<script>document.querySelector('textarea[name=\"txtAnswerSketch".$idQuestion."\"]').value='".$row -> TESTO."';</script>";
        }
    }
?>