<!DOCTYPE html>
<html>
    <?php 
        include 'connectionDB.php';
        $conn = openConnection();

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
                if (isset($_GET["btnDropTable"])) {
                deleteTable($conn, $idTable = $_GET["btnDropTable"]);    
            } elseif (isset($_GET["btnDropQuestion"])) {
                deleteQuestion($conn, $idQuestion = $_GET["btnDropQuestion"]);
            }
        }
        
        function deleteTable($conn, $idTable) {
            $storedProcedure = "CALL Eliminazione_Tabella_Esercizio(:idTable)";
            
            try {
                $result = $conn -> prepare($storedProcedure);
                $result -> bindValue(":idTable", $idTable);
                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione: '. $e -> getMessage();
            }
            
            header("Location: table_exercise.php");
        }

        function deleteQuestion($conn, $idQuestion) {
            $storedProcedure = "CALL Eliminazione_Quesito(:idQuestion)";
            
            try {
                $result = $conn -> prepare($storedProcedure);
                $result -> bindValue(":idQuestion", $idQuestion);
                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '. $e -> getMessage();
            }
        }

        closeConnection($conn);
    ?>
</html>