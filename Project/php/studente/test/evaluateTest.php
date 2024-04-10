<?php 
    function setValueSentMap($arrayIdQuestion) { // funzione attuata per restituire l'insieme delle risposte date dallo studente
        $mapArrayAnswer = array();
        $arrayNameCheckbox = array();

        for($i = 0; $i <= sizeof($arrayIdQuestion) - 1; $i++) {
            $varCheckbox = "checkbox"; // costruzione dinamica dei tag name a seconda dell'id del quesito
            $varCheckbox = $varCheckbox."".$arrayIdQuestion[$i];

            $varTextarea = "txtAnswerSketch";
            $varTextarea = $varTextarea."".$arrayIdQuestion[$i];

            if(isset($_POST[$varCheckbox])) {
                $mapArrayAnswer[$arrayIdQuestion[$i]] = $_POST[$varCheckbox];
            } elseif(isset($_POST[$varTextarea])) {
                $mapArrayAnswer[$arrayIdQuestion[$i]] = $_POST[$varTextarea];
            }         
        }

        return $mapArrayAnswer;
    }

    function setValueSolutionMap($conn, $arrayIdQuestion, $titleTest) { // funzione definita per garantire l'insieme delle soluzioni dei quesiti del test
        $mapArraySolution = array();

        for($i = 0; $i <= sizeof($arrayIdQuestion) - 1; $i++) {
            $arrayText = array();

            $type = getTypeQuestion($conn, $arrayIdQuestion[$i], $_SESSION["titleTestTested"]); 

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

            if($type == "CHIUSA") { // diversificazione della funzione fetch a seconda della tipologia della domanda, attuata poichè varia l'oggetto stdClass circoscritto
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

    function checkAnswer($conn, $manager, $arrayIdQuestion, $mapArrayAnswer, $mapArraySolution) { // metodo generale imposto come classificatore di funzioni
        foreach($arrayIdQuestion as $i) {  
            if(!$mapArrayAnswer[$i] == NULL) {
                $type = getTypeQuestion($conn, $i, $_SESSION["titleTestTested"]);

                if($type == "CHIUSA") { // diversifacazione dei metodi a seconda della tipologia del quesito
                    $outcome = checkOption($conn, $mapArrayAnswer[$i], $mapArraySolution[$i]);
                    $textAnswer = convertToString($mapArrayAnswer[$i]);
                } else {
                    $outcome = checkQuery($conn, $mapArrayAnswer[$i], $mapArraySolution[$i]);
                    $textAnswer = $mapArrayAnswer[$i];
                }
                
                insertAnswer($conn, $manager, $_SESSION["emailStudente"], $i, $_SESSION["titleTestTested"], $textAnswer, $outcome);
            }
        }
    }

    function checkOption($conn, $arrayIdOptionAnswer, $arrayIdOptionSolution) { // funzione attuata per convalidare la risposta al quesito chiuso
        if(sizeof($arrayIdOptionAnswer) == sizeof($arrayIdOptionAnswer)) { // controllo sulla dimensione, per accertarsi che il numero di risposte coincida
            foreach($arrayIdOptionAnswer as $a) { // ciclo definito per accertarsi se la risposta dello studente sia presente all'interno dell'insieme risolutivo della domanda
                if(!in_array($a, $arrayIdOptionSolution)) {
                    return 0; 
                }       
            }
    
            return 1;
        } else {
            return 0;
        }
    }

    function convertToString($array) {
        $str = "";

        foreach($array as $option) {
            $str = $str."".$option."|?|";
        }

        return $str;
    }

    function checkQuery($conn, $queryAnswer, $querySolution) { // funzione ideata per convalidare la risposta al quesito di codice
        try { // avviene l'execute della query risolutrice
            $resultSolution = $conn -> prepare($querySolution);

            $resultSolution -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $rowSolution = $resultSolution -> fetchAll(PDO::FETCH_OBJ);

        try { // avviene l'execute della query data dallo studente
            $resultAnswer = $conn -> prepare($queryAnswer);

            $resultAnswer -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $rowAnswer = $resultAnswer -> fetchAll(PDO::FETCH_OBJ);
        if($rowAnswer == $rowSolution) { // controllo ideato per accertarsi della validità della risposta dello studente
            return 1;
        } else {
            return 0;
        }
    }
    
    function insertAnswer($conn, $manager, $email, $idQuestion, $titleTest, $textAnswer, $outcome) {
        if(checkStateTest($conn, $email, $titleTest)) {
            $storedProcedure = "CALL Inserimento_Risposta(:emailStudente, :idQuesito, :titoloTest, :testoRisposta, :esito)";
            
            try {
                $stmt = $conn -> prepare($storedProcedure);
                $stmt -> bindValue(":emailStudente", $email);
                $stmt -> bindValue(":idQuesito", $idQuestion);
                $stmt -> bindValue(":titoloTest", $titleTest);
                $stmt -> bindValue(":testoRisposta", strtoupper($textAnswer));
                $stmt -> bindValue(":esito", $outcome);
                
                $stmt -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            //$document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento Risposta del Quesito id: '.$idQuestion.', immessa Studente email: '.$email.'', 'Timestamp' => date('Y-m-d H:i:s')];
            //writeLog($manager, $document); // scrittura log inserimento di una risposta
        }
    }

    function checkSketch($conn, $idQuestion, $titleTest, $queryAnswer) { // funzione ideata per garantire la correzione di una singola risposta ad un quesito di codice
        $sql = "SELECT Sketch_Codice.TESTO FROM Sketch_Codice WHERE (Sketch_Codice.ID_DOMANDA_CODICE=:idQuesito) AND (Sketch_Codice.TITOLO_TEST=:titoloTest) AND (Sketch_Codice.SOLUZIONE=1);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $row = $result -> fetch(PDO::FETCH_OBJ);
        $querySolution = $row -> TESTO;

        try { // run della query risolutrice
            $resultSolution = $conn -> prepare($querySolution);

            $resultSolution -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        try { // run della query data dallo studente
            $resultAnswer = $conn -> prepare($queryAnswer);
            
            $resultAnswer -> execute();
        } catch(PDOException $e) {
            return [null, $e -> getMessage()];
        }

        [$arrayFieldAnswer, $arrayFieldSolution] = getFieldName($resultAnswer, $resultSolution); // funzione attuata per estrapolare i nomi dei field dalla query data dallo studente e dalla query presente nel database
        $_SESSION["fieldAnswer"] = $arrayFieldAnswer;
        $_SESSION["fieldSolution"] = $arrayFieldSolution;

        $rowSolution = $resultSolution -> fetchAll(PDO::FETCH_ASSOC);
        $rowAnswer = $resultAnswer -> fetchAll(PDO::FETCH_ASSOC);
                
        return [$rowSolution, $rowAnswer];
    }
?> 