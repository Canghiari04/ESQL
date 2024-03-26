<?php     
    include "../../connectionDB.php";

    $conn = openConnection();
    $manager = openConnectionMongoDB();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnDropQuestion"])) {
            deleteQuestion($conn, $manager, $_POST["btnDropQuestion"]);
            header("Location: ../question.php");
            exit();
        } elseif(isset($_POST["btnDropAnswer"])) {
            deleteOption($conn, $manager, $_POST["btnDropAnswer"]);
            header("Location: ../specifics/specificQuestion.php");
            exit();
        }
    }
        
    function deleteQuestion($conn, $manager, $varQuestion) {
        $valuesQuestion = explode("|?|", $varQuestion); // acquisiti tutti i token necessari per la cancellazione del quesito
        
        $storedProcedure = "CALL Eliminazione_Quesito(:idQuesito, :titoloTest);";
            
        try {
            $result = $conn -> prepare($storedProcedure);
            $result -> bindValue(":idQuesito", $valuesQuestion[0]);
            $result -> bindValue(":titoloTest", $valuesQuestion[1]);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione quesito id: '.$valuesQuestion[0].'', 'Timestamp' => date('Y-m-d H:i:s')]; 
        writeLog($manager, $document); // scrittura log eliminazione di un quesito
    }

    function deleteOption($conn, $manager, $varAnswer) {
        $valuesOption = explode("|?|", $varAnswer); // acquisiti tutti i token necessari per eliminare un'opzione di risposta del quesito

        if($valuesOption[0] == "CHIUSA") { // diversificazione della procedure a seconda della tipologia
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

        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione risposta id: '.$valuesOption[1].'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document); // scrittura log eliminazione di una risposta
    }

    closeConnection($conn);
?>