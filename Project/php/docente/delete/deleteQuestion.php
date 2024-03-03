<!DOCTYPE html>
<html>
    <?php     
        include "../../connectionDB.php";

        $conn = openConnection();
        $manager = openConnectionMongoDB();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnDropQuestion"])) {
                deleteQuestion($conn, $varQuestion = $_POST["btnDropQuestion"]);

                /* scrittura log eliminazione di un record relativo alla tabella Quesito */
                $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione quesito id: '.$idQuestion.'', 'Timestamp' => date('Y-m-d H:i:s')];
                writeLog($manager, $document);

                header("Location: ../question.php");
                exit;
            } elseif(isset($_POST["btnDropOption"])) {
                deleteOption($conn, $varOption = $_POST["btnDropOption"]);
                
                /* manca writelog */

                header("Location: ../specifics/specificQuestion.php");
                exit;
            }
        }
        
        function deleteQuestion($conn, $varQuestion) {
            $valuesQuestion = explode('?', $varQuestion);

            $storedProcedure = "CALL Eliminazione_Quesito(:idQuesito, :titoloTest);";
            
            try {
                $result = $conn -> prepare($storedProcedure);
                $result -> bindValue(":idQuesito", $valuesQuestion[0]);
                $result -> bindValue(":titoloTest", $valuesQuestion[1]);

                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        }

        function deleteOption($conn, $varOption) {
            $valuesOption = explode('?', $varOption);

            if($valuesOption[0] == "CHIUSA") {
                $storedProcedure = "CALL Eliminazione_Opzione_Risposta(:idRisposta, :idQuesito, :titoloTest);";
            } else {
                $storedProcedure = "CALL Eliminazione_Sketch_Codice(:idRisposta, :idQuesito, :titoloTest);";
            }
            
            var_dump($storedProcedure);

            try {
                $stmt = $conn -> prepare($storedProcedure);
                $stmt -> bindValue(":idRisposta", $valuesOption[1]);
                $stmt -> bindValue(":idQuesito", $valuesOption[2]);              
                $stmt -> bindValue(":titoloTest", $valuesOption[3]);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        }

        closeConnection($conn);
    ?>
</html>