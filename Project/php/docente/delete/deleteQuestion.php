<?php     
    include "../../connectionDB.php";

    $conn = openConnection();
    $manager = openConnectionMongoDB();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnDropQuestion"])) {
            deleteQuestion($conn, $manager, $varQuestion = $_POST["btnDropQuestion"]);
            header("Location: ../question.php");
            exit();
        } elseif(isset($_POST["btnDropOption"])) {
            deleteOption($conn, $manager, $varOption = $_POST["btnDropOption"]);
            header("Location: ../specifics/specificQuestion.php");
            exit();
        }
    }
        
    function deleteQuestion($conn, $manager, $varQuestion) {
        /* explode attuato per acquisire tutti i token del quesito necessari per richiamare la procedure */
        $valuesQuestion = explode('|?|', $varQuestion);
        
        var_dump($valuesQuestion);
        $storedProcedure = "CALL Eliminazione_Quesito(:idQuesito, :titoloTest);";
            
        try {
            $result = $conn -> prepare($storedProcedure);
            $result -> bindValue(":idQuesito", $valuesQuestion[0]);
            $result -> bindValue(":titoloTest", $valuesQuestion[1]);

            $result -> execute();
            
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* scrittura log eliminazione di un record relativo alla tabella Quesito */
        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione quesito id: '.$valuesQuestion[0].'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document);
        
    }

    /* metodo che permette l'eliminazione di una risposta ad un specifico quesito */
    function deleteOption($conn, $manager, $varOption) {
        /* explode attuato per acquisire tutti i token della risposta necessari per richiamare la procedure */
        $valuesOption = explode('|?|', $varOption);

        /* diversificazione della procedure a seconda della tipologia */
        if($valuesOption[0] == "CHIUSA") {
            $storedProcedure = "CALL Eliminazione_Opzione_Risposta(:idRisposta, :idQuesito, :titoloTest);";
        } else {
            $storedProcedure = "CALL Eliminazione_Sketch_Codice(:idRisposta, :idQuesito, :titoloTest);";
        }

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":idRisposta", $valuesOption[1]);
            $stmt -> bindValue(":idQuesito", $valuesOption[2]);              
            $stmt -> bindValue(":titoloTest", $valuesOption[3]);

            $stmt -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* scrittura log eliminazione di una risposta riferita ad un quesito */
        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione risposta id: '.$valuesOption[1].'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document);
    }

    closeConnection($conn);
?>