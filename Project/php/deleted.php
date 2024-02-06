<!DOCTYPE html>
<html>
    <?php 
        include 'connectionDB.php';
        $conn = openConnection();

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (isset($_GET["btnDropTable"])) {
                deleteTable($conn, $idTable = $_GET["btnDropTable"]);    
            } else {
                /* mancano tutti i casi paralleli */
            }
        }
        
        closeConnection($conn);
    ?>
    
    <?php
        /* mettere l'eliminazione nelle stored procedure */
        function deleteTable($conn, $idTable){
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
    ?>
</html>