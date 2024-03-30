<?php
    function checkCompletedTest($conn, $titleTest) { // funzione attuata per accertarsi che i test visualizzati abbiano quesiti caratterizzati ognuno di essi da una soluzione
        $arrayIdQuestion = getQuestionTest($conn, $titleTest);

        if(sizeof($arrayIdQuestion) > 0) { // controllo definito per accertarsi che il test abbia dei quesiti associati ad esso
            for($i = 0; $i <= sizeof($arrayIdQuestion) - 1; $i++) {
                $type = getTypeQuestion($conn, $arrayIdQuestion[$i], $titleTest);
                
                if($type == "CHIUSA") { // diversificazione della query a seconda della tipologia 
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
                
                $numRows = $result -> rowCount();
                if($numRows == 0) { // controllo ideato per accertarsi se il quesito abbia almeno una soluzione
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

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
        if($numRows > 0) { // controllo definito per ovviare la possibilità di visualizzare a schermo warning built-in di php
            $row = $result -> fetch(PDO::FETCH_OBJ);
            return $row -> STATO;
        } else {
            return " ";
        }
    }

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

    function checkNumAnswer($conn, $email, $titleTest) { // funzione attuata per estrapolare il numero di risposte immesse dallo studente
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

    function checkChecked($conn, $email, $idQuestion, $titleTest) { // funzione definita per individuare tutti quei quesiti a cui lo studente abbia già risposto in tentativi precedenti
        $sql = "SELECT TESTO FROM Risposta WHERE (Risposta.EMAIL_STUDENTE=:emailStudente) AND (Risposta.ID_QUESITO=:idQuesito) AND (Risposta.TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $email);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $questionSolutions = array(); // array che conterrà l'insieme delle opzioni di risposta immesse per quesiti a domanda chiusa

        $numRows = $result -> rowCount();
        if($numRows > 0) { 
            $row = $result -> fetch(PDO::FETCH_OBJ);
            $questionSolutions =  explode('|?|', $row -> TESTO); // estrapolazione degli id mediante lo split del carattere speciale ideato
        }
        
        return $questionSolutions;
    }

    function checkAnswered($conn, $email, $idQuestion, $titleTest) { // funzione ideata per visualizzare o meno risposte a quesiti di codice immesse dallo studente
        $sql = "SELECT TESTO FROM Risposta WHERE (Risposta.EMAIL_STUDENTE=:emailStudente) AND (Risposta.ID_QUESITO=:idQuesito) AND (Risposta.TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $email);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $numRows = $result -> rowCount();
        if($numRows > 0){      
            $row = $result -> fetch(PDO::FETCH_OBJ); 
            echo "<script>document.querySelector('textarea[name=\"txtAnswerSketch".$idQuestion."\"]').value=".json_encode($row -> TESTO).";</script>"; // script ideato per sovrascrivere il contenuto della textarea con la risposta immessa dallo studente
        }
    }

    function checkSolution($conn, $idQuestion, $titleTest) { // funzione simile alla precedente, ma orientata per la sola visualizzazione delle soluzioni del test
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
            echo "<script>document.querySelector('textarea[name=\"txtAnswerSketch".$idQuestion."\"]').value=".json_encode($row -> TESTO).";</script>"; // script ideato per sovrascrivere il contenuto della textarea con la risposta immessa dallo studente
        }
    }
?>