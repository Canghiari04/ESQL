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

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
                if (isset($_GET["btnDropTable"])) {
                echo 'cia';
                
                deleteTable($conn, $idTable = $_GET["btnDropTable"]);
                deleteTableExercise($conn, $idTable = $_GET["btnDropTable"]);

                //log mongodb
                $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione tabella id: ' .$idTable. '', 'Timestamp' => date('Y-m-d H:i:s')];
                writeLog($manager, $document);

                header("Location: ../table_exercise.php");
            } elseif (isset($_GET["btnDropQuestion"])) {
                deleteQuestion($conn, $idQuestion = $_GET["btnDropQuestion"]);

                //log mongodb
                $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione quesito id: ' .$idQuestion. '', 'Timestamp' => date('Y-m-d H:i:s')];
                writeLog($manager, $document);

                header("Location: ../question.php");
            }
        }
        
        function deleteTableExercise($conn, $id) {
            $storedProcedure = "CALL Eliminazione_Tabella_Esercizio(:id);";
            
            try {
                $result = $conn -> prepare($storedProcedure);
                $result -> bindValue(":id", $id);

                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        function deleteTable($conn, $id) {
            $sql = "SELECT NOME FROM Tabella_Esercizio WHERE (ID=:id);";
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":id", $id);

                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            $row = $result -> fetch(PDO::FETCH_ASSOC);
            $nome = $row['NOME'];

            $sql = "DROP TABLE '.$nome.';";

            try {
                $result = $conn -> prepare($sql);

                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        function deleteQuestion($conn, $id) {
            $storedProcedure = "CALL Eliminazione_Quesito(:id);";
            
            try {
                $result = $conn -> prepare($storedProcedure);
                $result -> bindValue(":id", $id);

                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        closeConnection($conn);
    ?>
</html>