<?php 
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();

    if($_SERVER["REQUEST_METHOD"] == "POST") {   
        if(isset($_POST["btnSendTest"])) {
            /* acquisisco l'insieme di tutti gli id dei quesiti che compongano il test in questione */
            $arrayIdQuestion = getIdTestQuestions($conn, $_SESSION["titleTestTested"]);

            /* da cui formulo i name di tutti i tag input del test, acquisendo i valori posti al loro interno */
            $mapArrayAnswer = setValuesSentMap($arrayIdQuestion);
            $mapArraySolution = setValueSolutionMap($conn, $arrayIdQuestion, $_SESSION["titleTestTested"]); 

            checkAnswer($conn, $arrayIdQuestion, $mapArrayAnswer, $mapArraySolution);

            header("Location: ../view/viewTest.php");
            exit;
        }
    }

    function getIdTestQuestions($conn, $titleTest) {
        $arrayIdQuestion = array();

        // DIVERSIFICO PER JOIN TRA DOMANDA CHIUSA E DOMANDA CODICE
        $sql = "SELECT ID FROM Quesito WHERE (Quesito.TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
            array_push($arrayIdQuestion, $row -> ID);
        }

        return $arrayIdQuestion;
    }

    function setValuesSentMap($arrayIdQuestion) {
        $mapArrayAnswer = array();
        $arrayNameCheckbox = array();

        for($i = 0; $i <= sizeof($arrayIdQuestion) - 1; $i++) {
            $varCheckbox = "checkbox";
            $varCheckbox = $varCheckbox."".$arrayIdQuestion[$i];

            $varTextarea = "txtAnswerSketch";
            $varTextarea = $varTextarea."".$arrayIdQuestion[$i];

            if(isset($_POST[$varCheckbox])) {
                $mapArrayAnswer[$arrayIdQuestion[$i]] = $_POST[$varCheckbox];
            } else {
                $mapArrayAnswer[$arrayIdQuestion[$i]] = $_POST[$varTextarea];
            }         
        }

        return $mapArrayAnswer;
    }

    /* funzione che restituisce l'insieme delle soluzioni dei quesiti che compongono il test in questione, ordinate secondo coppia chiave -> valore */
    function setValueSolutionMap($conn, $arrayIdQuestion, $titleTest) {
        $mapArraySolution = array();

        for($i = 0; $i <= sizeof($arrayIdQuestion) - 1; $i++) {
            $arrayText = array();
            $type = getTypeQuestion($conn, $arrayIdQuestion[$i], $_SESSION["titleTestTested"]); 

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

            if($type == "CHIUSA") {
                while($row = $result -> fetch(PDO::FETCH_ASSOC)){
                    foreach($row as $item) {
                        array_push($arrayText, $item);
                    }    

                    $mapArraySolution[$arrayIdQuestion[$i]] = $arrayText;
                }
            } else {
                $row = $result -> fetch(PDO::FETCH_OBJ);
                $mapArraySolution[$arrayIdQuestion[$i]] = $row -> TESTO;
            }
        }

        return $mapArraySolution;
    }

    function getTypeQuestion($conn, $idQuestion, $titleTest) {
        $sql = "SELECT * FROM Domanda_Chiusa WHERE (Domanda_Chiusa.ID_DOMANDA_CHIUSA=:idQuesito) AND (Domanda_Chiusa.TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
            $numRows = $result -> rowCount();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* controllo della tipologia del quesito, basata sulla query eseguita precedentemente */
        if($numRows > 0) {
            return "CHIUSA";
        } else {
            return "CODICE";
        }
    }

    // STAMPA CORRETTA, OSSIA SINCRONIZZATA TRA LE RISPOSTE DEI QUESITI DATE E DELLE SOLUZIONI ALL'INTERNO DEL DATABASE
    function checkAnswer($conn, $arrayIdQuestion, $mapArrayAnswer, $mapArraySolution) {
        foreach($arrayIdQuestion as $i) {  
            if(!$mapArrayAnswer[$i] == NULL) {
                /* acquisisce la tipologia della domanda, in base all'id del quesito e al test di appartenenza */
                $type = getTypeQuestion($conn, $i, $_SESSION["titleTestTested"]);

                if($type == "CHIUSA") {
                    $outcome = checkOption($conn, $mapArrayAnswer[$i], $mapArraySolution[$i]);
                    $textAnswer = convertToString($mapArrayAnswer[$i]);
                } else {
                    $outcome = checkQuery($conn, $mapArrayAnswer[$i], $mapArraySolution[$i]);
                    $textAnswer = $mapArrayAnswer[$i];
                }
                
                insertAnswer($conn, $_SESSION["emailStudente"], $i, $_SESSION["titleTestTested"], $textAnswer, $outcome);
            }
        }
    }

    // POTREBBE DARE ERRORE QUALORA I VETTORI NON DOVESSERO CONTENERE VALORI 
    function checkOption($conn, $arrayIdOptionAnswer, $arrayIdOptionSolution) {
        /* primo controllo sulla dimensione dei due array, per verificare se il numero di risposte date coincida con il vettore contenente l'insieme delle risposte risolutive della domanda di riferiemento */
        if(sizeof($arrayIdOptionAnswer) == sizeof($arrayIdOptionAnswer)) {
            /* controllo che l'array contenente tutti gli id delle risposte corrette al quesito siano presenti anche rispetto alla risposta data */
            foreach($arrayIdOptionAnswer as $a) {
                if(!in_array($a, $arrayIdOptionSolution)) {
                    $_SESSION["correctionTest"] = false;
                    return 0; 
                }       
            }
    
            return 1;
        } else {
            $_SESSION["correctionTest"] = false;
            return 0;
        }
    }

    function convertToString($array) {
        $str = "";

        foreach($array as $option) {
            /* utilizzo di un carattere speciale in maniera tale da poter concatenare le risposte e poi recuperarle per successive visualizzazioni */
            $str = $str."".$option."|?|";
        }

        return $str;
    }

    function checkQuery($conn, $queryAnswer, $querySolution) {
        /* run della query risolutrice per ottenerne il risultato, in righe e colonne, che possa essere confrontato rispetto alla risposta data */
        try {
            $resultSolution = $conn -> prepare($querySolution);

            $resultSolution -> execute();
            $rowSolution = $resultSolution -> fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* run della query posta dallo studente, successivamente oggetto di confronto rispetto alla query risolutrice */
        try {
            $resultAnswer = $conn -> prepare($queryAnswer);

            $resultAnswer -> execute();
            $rowAnswer = $resultAnswer -> fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        if ($rowAnswer == $rowSolution) {
            return 1;
        } else {
            $_SESSION["correctionTest"] = false;
            return 0;
        }
    }
    
    function insertAnswer($conn, $email, $idQuestion, $titleTest, $textAnswer, $outcome) {
        $storedProcedure = "CALL Inserimento_Risposta(:emailStudente, :idQuesito, :titoloTest, :testoRisposta, :esito)";

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":emailStudente", $email);
            $stmt -> bindValue(":idQuesito", $idQuestion);
            $stmt -> bindValue(":titoloTest", $titleTest);
            $stmt -> bindValue(":testoRisposta", (string)$textAnswer);
            $stmt -> bindValue(":esito", $outcome);
            
            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }

    closeConnection($conn);
?> 