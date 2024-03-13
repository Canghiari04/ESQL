<?php 
    /* funzione restituente la mappa contenitrice di tutte le risposte date dallo studente, ordinate secondo il numero progressivo dei quesiti */
    function setValueSentMap($arrayIdQuestion) {
        $mapArrayAnswer = array();
        $arrayNameCheckbox = array();

        for($i = 0; $i <= sizeof($arrayIdQuestion) - 1; $i++) {
            /* costruzione del tag name di ogni input definito a livello di HTML */
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

    /* funzione che restituisce l'insieme delle soluzioni dei quesiti che compongono il test in questione, ordinate secondo la numerazione progressiva dei quesiti */
    function setValueSolutionMap($conn, $arrayIdQuestion, $titleTest) {
        $mapArraySolution = array();

        for($i = 0; $i <= sizeof($arrayIdQuestion) - 1; $i++) {
            $arrayText = array();

            /* è definita la tipologia del quesito, in maniera tale da diversificare la query che interrogherà le collezione di riferimento */
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
                /* distinzione, nei due rami del costrutto, dell'argomento dato al metodo fetch poichè le domande chiuse sono caratterizzate da array di possibili soluzioni */
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

    function checkAnswer($conn, $arrayIdQuestion, $mapArrayAnswer, $mapArraySolution) {
        foreach($arrayIdQuestion as $i) {  
            if(!$mapArrayAnswer[$i] == NULL) {
                /* acquisisce la tipologia della domanda, in base all'id del quesito e al test di appartenenza */
                $type = getTypeQuestion($conn, $i, $_SESSION["titleTestTested"]);

                /* in base alla tipologia è attuato un controllo differente */
                if($type == "CHIUSA") {
                    $outcome = checkOption($conn, $mapArrayAnswer[$i], $mapArraySolution[$i]);

                    /* conversione in una stringa dell'array di risposte, dato che contiene gli id di tipo numerico */
                    $textAnswer = convertToString($mapArrayAnswer[$i]);
                } else {
                    $outcome = checkQuery($conn, $mapArrayAnswer[$i], $mapArraySolution[$i]);
                    $textAnswer = $mapArrayAnswer[$i];
                }
                
                /* inserimento all'interno della tabella Risposta */
                insertAnswer($conn, $_SESSION["emailStudente"], $i, $_SESSION["titleTestTested"], $textAnswer, $outcome);
            }
        }
    }

    function checkOption($conn, $arrayIdOptionAnswer, $arrayIdOptionSolution) {
        /* primo controllo sulla dimensione dei due array, per verificare se il numero di risposte date coincida con il vettore contenente l'insieme delle risposte risolutive della domanda di riferimento */
        if(sizeof($arrayIdOptionAnswer) == sizeof($arrayIdOptionAnswer)) {
            /* controllo definito per accertarsi se la risposta data contenga gli id di tutte le opzioni risolutrici del quesito*/
            foreach($arrayIdOptionAnswer as $a) {
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
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $rowSolution = $resultSolution -> fetchAll(PDO::FETCH_OBJ);

        /* run della query posta dallo studente, successivamente oggetto di confronto rispetto alla query risolutrice */
        try {
            $resultAnswer = $conn -> prepare($queryAnswer);

            $resultAnswer -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $rowAnswer = $resultAnswer -> fetchAll(PDO::FETCH_OBJ);

        if ($rowAnswer == $rowSolution) {
            return 1;
        } else {
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
            $stmt -> bindValue(":testoRisposta", $textAnswer);
            $stmt -> bindValue(":esito", $outcome);
            
            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }

    function checkSketch($conn, $idQuestion, $titleTest, $queryAnswer) {
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

        /* run della query risolutrice per ottenerne il risultato, in righe e colonne, che possa essere confrontato rispetto alla risposta data */
        try {
            $resultSolution = $conn -> prepare($querySolution);

            $resultSolution -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $rowSolution = $resultSolution -> fetchAll(PDO::FETCH_OBJ);

        /* run della query posta dallo studente, successivamente oggetto di confronto rispetto alla query risolutrice */
        try {
            $resultAnswer = $conn -> prepare($queryAnswer);

            $resultAnswer -> execute();
        } catch(PDOException $e) {
            return [0, $e -> getMessage()];
        }
        
        $rowAnswer = $resultAnswer -> fetchAll(PDO::FETCH_OBJ);
        
        if ($rowAnswer == $rowSolution) {
            return [1, null];
        } else {
            return [0, null];
        }
    }
?> 