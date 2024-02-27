<!DOCTYPE html>
<html>
    <head>
        <?php
            include '../../connectionDB.php'
        ?>
    </head>
    <?php 
        $conn = openConnection();
        $manager = openConnectionMongoDB();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['btnDropQuestion'])) {
                deleteQuestion($conn, $idQuestion = $_POST['btnDropQuestion']);

                /* scrittura log eliminazione di un record relativo alla tabella Quesito */
                $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione quesito id: '.$idQuestion.'', 'Timestamp' => date('Y-m-d H:i:s')];
                writeLog($manager, $document);
            } elseif(isset($_POST['btnDropAnswer'])) {
                deleteAnswer($conn, $varAnswer = $_POST['btnDropAnswer']);

                /* manca writelog */
            }

            header('Location: ../question.php');
        }
        
        function deleteQuestion($conn, $id) {
            $storedProcedure = 'CALL Eliminazione_Quesito(:id);';
            
            try {
                $result = $conn -> prepare($storedProcedure);
                $result -> bindValue(':id', $id);

                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        function deleteAnswer($conn, $varAnswer) {
            $valuesComposition = explode('?', $varAnswer);

            if(getTypeQuestion($conn, $valuesComposition[0]) == 'CHIUSA') {
                $storedProcedure = 'CALL Eliminazione_Opzione_Risposta(:idQuesito, :testo);';
            } else {
                $storedProcedure = 'CALL Eliminazione_Sketch_Codice(:idQuesito, :testo);';
            }
            
            try {
                $stmt = $conn -> prepare($storedProcedure);

                $stmt -> bindValue(':idQuesito', $valuesComposition[0]);              
                $stmt -> bindValue(':testo', $valuesComposition[1]);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        function getTypeQuestion($conn, $idQuestion) {
            $sql = 'SELECT * FROM Quesito JOIN Domanda_Chiusa ON (ID = ID_DOMANDA_CHIUSA) WHERE (Quesito.ID = :idQuesito);';

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(':idQuesito', $idQuestion);

                $result -> execute();
                $numRows = $result -> rowCount();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().' <br>';
            }

            if($numRows > 0) {
                return 'CHIUSA';
            } else {
                return 'CODICE';
            }
        }

        closeConnection($conn);
    ?>
</html>