<?php 
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();

    if($_SERVER["REQUEST_METHOD"] == "POST") {   
        if(isset($_POST["btnSaveExit"])) {
            //DOVREI CREARE DUE METODI CHE MI RESTITUISCANO GLI ID DEI QUESITI

            $arrayIdQuestion = getIdTestQuestions($conn, $_SESSION["titleTestTested"]);
            /*
             * ACQUISISCO GLI ID DEI QUESITI DIFFERENZIANDOLI IN BASE ALLA TIPOLOGIA ALL'INTERNO DI DUE LISTE DIFFERENTI 
             * ADOTTO DUE CICLI FOREACH DA CUI VADO A SALVARMI IL TESTO DELLE RISPOSTE ALL'INTERNO DI APPOSITE STRUTTURE
             * INFINE VADO AD INSERIRE ALL'INTERNO DELLA COLLEZIONE RISPOSTA MEDIANTE LA SINCRONIZZAZIONE DEI DUE ARRAY
             * 
             * OCCORRE IL CONTROLLO PER OSSERVARE SE TUTTE LE DOMANDE ABBIANO UNA RISPOSTA, E POI CHE SIANO TUTTE CORRETTE PER AFFERMARE IL CAMBIAMENTO DELLO STATO DEL COMPLETAMENTO 
             * PER AFFERMARE SE TUTTE LE DOMANDE HANNO RISPOSTA FACCIO LA SOMMA DEI DUE ARRAY CHE CONTENGONO LE RISPOSTE E BASTA UNA SINGOLA VARIABILE BOOLEANA PER TENERE TRACCIA DELLA POSSIBILITÀ CHE SIANO TUTTE CORRETTE O MENO
             */
        } elseif(isset($_POST["btnSendTest"])) {
            /* acquisisco l'insieme di tutti gli id dei quesiti che compongano il test in questione */
            $arrayIdQuestion = getIdTestQuestions($conn, $_SESSION["titleTestTested"]);

            /* da cui formulo i name di tutti i tag input del test, acquisendo i valori posti al loro interno */
            $mapArrayAnswer = setValuesSentMap($arrayIdQuestion);
            $mapArraySolution = setValueSolutionMap($conn, $arrayIdQuestion, $_SESSION["titleTestTested"]);  

            print_r($mapArraySolution);
            //var_dump(checkOption($conn, $arrayIdCloseQuestion, $_SESSION["titleTestTested"], $arrayValuesOption));
            

            /* acquisisco le soluzioni dei quesiti del test */
            //[$arraySolutionOption, $arraySolutionSketch] = getSolutionQuestions($arrayIdCloseQuestion, $arrayIdOpenQuestion);
        }
    }

    function checkOption($conn, $arrayIdCloseQuestion, $titleTest, $arrayIdOption) {
        $arrayCorrectIdOption = array();
        
        $sql = "SELECT ID FROM Opzione_Risposta WHERE (ID_DOMANDA_CHIUSA=:idQuesito) AND (TITOLO_TEST=:titoloTest) AND (SOLUZIONE=1);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();

            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                $idCorrectOption = $row -> ID;
                array_push($arrayCorrectIdOption, $idCorrectOption);
            }
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }


        /* primo controllo sulla dimensione dei due array, per verificare se il numero di risposte date coincida con il vettore contenente l'insieme delle risposte risolutive della domanda di riferiemento */
        if(sizeof($arrayIdOption) == sizeof($arrayCorrectIdOption)) {
            /* controllo che l'array contenente tutti gli id delle risposte corrette al quesito siano presenti anche rispetto alla risposta data */
            foreach($arrayIdOption as $a) {
                if(!in_array($a, $arrayCorrectIdOption)) {
                    return 0; 
                }       
            }

            return 1;
        } else {
            return 0;
        }
    }

    function checkQuery($conn, $idQuestion, $titleTest, $queryAnswer) {
        $sql = "SELECT TESTO FROM Sketch_Codice WHERE (ID_DOMANDA_CODICE=:idQuesito) AND (TITOLO_TEST=:titoloTest) AND (SOLUZIONE=1);";

        /* tramite l'esecuzione sottostante si acquisisce la query risolutrice del quesito posto */
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
            $row = $result -> fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $querySolution = $row -> TESTO;

        /* run della query risolutrice per ottenerne il risultato, in righe e colonne, che possa essere confrontato rispetto alla risposta data */
        try {
            $stmtSolution = $conn -> prepare($querySolution);

            $stmtSolution -> execute();
            $resultSolution = $stmtSolution -> fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* run della query posta dallo studente, successivamente oggetto di confronto rispetto alla query risolutrice */
        try {
            $stmtAnswer = $conn -> prepare($queryAnswer);

            $stmtAnswer -> execute();
            $resultAnswer = $stmtAnswer -> fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            echo "<script>document.querySelector('.input-tips').value=".json_encode($e -> getMessage(), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS).";</script>";
        }

        return ($resultAnswer == $resultSolution);
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

            if(getTypeQuestion($conn, $arrayIdQuestion[$i]) == "CHIUSA") {
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

            if(getTypeQuestion($conn, $arrayIdQuestion[$i]) == "CHIUSA") {
                while($row = $result -> fetch(PDO::FETCH_ASSOC)){
                    foreach($row as $item) {
                        array_push($arrayText, $item);
                    }    

                    $mapArraySolution[$arrayIdQuestion[$i]] = $arrayText;
                }
            } else {
                $row = $result -> fetch(PDO::FETCH_OBJ);
                
                $mapArraySolution[$arrayIdQuestion[$i]] = $row;
            }
        }

        return $mapArraySolution;
    }

    function getTypeQuestion($conn, $idQuestion) {
        $sql = "SELECT * FROM Domanda_Chiusa WHERE (Domanda_Chiusa.ID_DOMANDA_CHIUSA=:idQuesito);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);

            $result -> execute();
            $numRows = $result -> rowCount();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        if($numRows > 0) {
            return "CHIUSA";
        } else {
            return "CODICE";
        }
    }

    function getSolutionQuestions($conn, $arrayIdCloseQuestion, $arrayIdOpenQuestion, $titleTest) {
        $arraySolutionOption -> array();
        $arraySolutionSketch -> array();

        for($i = 0; $i <= sizeof($arrayIdCloseQuestion) - 1; $i++) {
            $sqlSolutionOption = "SELECT ID, TESTO FROM Quesito, Opzione_Risposta WHERE (Quesito.ID=Opzione_Risposta.ID_DOMANDA_CHIUSA) AND (Opzione_Risposta.ESITO=true) AND (Quesito.ID=:idQuesito) AND (Quesito.TITOLO_TEST=:titoloTest);";
            
            try {
                $resultSolutionOption = $conn -> prepare($sqlSolutionOption);
                $resultSolutionOption -> bindValue(":idQuesito", $arrayIdCloseQuestion[$i]);
                $resultSolutionOption -> bindValue(":titoloTest", $titleTest);
                
                $resultSolutionOption -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
            
            while($rowSolutionOption = $resultSolutionOption -> fetch(PDO::FETCH_OBJ)) {
                array_push($arraySolutionOption, $rowSolutionOption -> ID."|?|".$rowSolutionOption -> TESTO);
            }
        }

        for($i = 0; $i <= sizeof($arrayIdOpenQuestion) - 1; $i++) {
            $sqlSolutionSketch = "SELECT ID,TESTO FROM Quesito, Sketch_Codice WHERE (Quesito.ID=Sketch_Codice.ID_DOMANDA_CODICE) AND (Sketch_Codice.ESITO=true) AND (Quesito.ID=:idQuesito) AND (Quesito.TITOLO_TEST=:titoloTest);";
            
            try {
                $resultSolutionSketch = $conn -> prepare($sqlSolutionSketch);
                $resultSolutionSketch -> bindValue(":idQuesito", $arrayIdOpenQuestion[$i]);
                $resultSolutionSketch -> bindValue(":titoloTest", $titleTest);
                
                $resultSolutionSketch -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
            
            while($rowSolutionSketch = $resultSolutionSketch -> fetch(PDO::FETCH_OBJ)) {
                array_push($arraySolutionOption, $rowSolutionSketch -> ID."|?|".$rowSolutionSketch -> TESTO);
            }
        }
    }

    closeConnection($conn);
?> 