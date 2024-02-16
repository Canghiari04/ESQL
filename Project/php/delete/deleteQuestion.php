<!DOCTYPE html>
<html>
    <head>
        <?php
            include '../connectionDB.php'
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

                header('Location: ../question.php');
            }
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

        closeConnection($conn);
    ?>
</html>