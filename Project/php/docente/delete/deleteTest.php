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
            if(isset($_POST['btnDropTest'])) {
                deleteTest($conn, $testTitle = $_POST['btnDropTest']);
            } elseif(isset($_POST['btnUpdateTest'])) {
                updateTest($conn, $testTitle = $_POST['btnUpdateTest']);
            } elseif(isset($_POST['btnDropComposition'])){
                deleteComposition($conn, $varComposition = $_POST['btnDropComposition']);
            }

            header('Location: ../test.php');
            exit; 
        }

        function deleteTest($conn, $title) {
            $storedProcedure = 'CALL Eliminazione_Test(:titolo);';
            
            try {
                $stmt = $conn -> prepare($storedProcedure);
                $stmt -> bindValue(':titolo', $title);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        function updateTest($conn, $title) {
            $storedProcedure = 'CALL Aggiornamento_Test(:titolo);';
            
            try {
                $stmt = $conn -> prepare($storedProcedure);
                $stmt -> bindValue(':titolo', $title);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        function deleteComposition($conn, $varComposition) {
            $valuesComposition = explode('?', $varComposition);
            $storedProcedure = 'CALL Eliminazione_Composizione(:titolo, :idQuesito);';
            
            try {
                $stmt = $conn -> prepare($storedProcedure);

                $stmt -> bindValue(':titolo', $valuesComposition[0]);              
                $stmt -> bindValue(':idQuesito', $valuesComposition[1]);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        closeConnection($conn);
    ?>
</html>