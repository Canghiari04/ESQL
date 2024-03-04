<?php 
    include '../../connectionDB.php';
    
    session_start();
    
    $conn = openConnection();
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnCheckAnswerOption"])) {
            $idQuestion = $_POST["btnCheckAnswerOption"];
            $values = $_POST['checkbox'];
            
            $outcome = checkOption($conn, $idQuestion, $titleTest, $values);
            
            $textAnswer = convertToString($values);
            
            insertAnswer($conn, $_SESSION['emailStudente'], $idQuestion, $textAnswer, $outcome);
        } elseif($_POST["btnCheckAnswerSketch"]) {
            // FARE EXPLODE QUA DENTRO -->
            $values = $_POST["btnCheckAnswerSketch"];
            $textAnswer = $_POST["txtAnswerSketch"];
            
            echo 'sono dentro';
            // SEMBRA CHE FUNZIONI MA DA TESTARE PER QUERY PIÙ COMPLESSE
            $outcome = checkQuery($conn, $idQuestion, $textAnswer);

            var_dump($outcome);

            insertAnswer($conn, $_SESSION['emailStudente'], $idQuestion, $textAnswer, $outcome);
        }
    }

    function checkOption($conn, $idQuestion, $titleTest, $arrayIdOption) {
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

    function convertToString($arrayIdOption) {
        foreach($arrayIdOption as $option) {

            /* utilizzo un carattere speciale in maniera tale da poter concatenare le risposte e poi recuperarle per successive visualizzazioni */
            $str = $str."".$option."|?|";
        }

        $str = substr($str, -1);
        return $str;
    }

    function checkQuery($conn, $idQuestion, $titleTest, $queryAnswer) {
        $arrayCorrectIdSketch = array(); 
        
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
            // DOVREI STAMPARE L'ERRORE DELL'ECCEZIONE ALL'INTERNO DELLA TEXTAREA SPECIFICA
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        return ($resultAnswer == $resultSolution);
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

    closeConnection($conn);
?>