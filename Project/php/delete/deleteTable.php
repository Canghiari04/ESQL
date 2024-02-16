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
            if(isset($_POST['btnDropTable'])) {
                deleteTable($conn, $idTable = $_POST['btnDropTable']);
                deleteTableExercise($conn, $idTable = $_POST['btnDropTable']);

                /* scrittura log eliminazione di un record appartenente a Tabella_Esercizio */
                $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione tabella id: '.$idTable.'', 'Timestamp' => date('Y-m-d H:i:s')];
                writeLog($manager, $document);

                header('Location: ../table_exercise.php');
            } 
        }

        function deleteTable($conn, $id) {
            $sql = 'SELECT NOME FROM Tabella_Esercizio WHERE (ID=:id);';
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(':id', $id);

                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            $row = $result -> fetch(PDO::FETCH_ASSOC);
            $nome = $row['NOME'];

            $sql = 'DROP TABLE '.$nome.';';

            try {
                $result = $conn -> prepare($sql);

                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        function deleteTableExercise($conn, $id) {
            $storedProcedure = 'CALL Eliminazione_Tabella_Esercizio(:id);';
            
            try {
                $stmt = $conn -> prepare($storedProcedure);
                $stmt -> bindValue(':id', $id);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        closeConnection($conn);
    ?>
</html>