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
            } elseif(isset($_POST['btnDropQuestion'])){
                deleteQuestion($conn, $varComposition = $_POST['btnDropQuestion']);
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

        function deleteQuestion($conn, $varQuestion) {
            $valuesQuestion = explode('?', $varQuestion);
            $storedProcedure = 'CALL Eliminazione_Composizione(:titolo, :idQuesito);';
            
            try {
                $stmt = $conn -> prepare($storedProcedure);

                $stmt -> bindValue(':titolo', $valuesQuestion[0]);              
                $stmt -> bindValue(':idQuesito', $valuesQuestion[1]);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        closeConnection($conn);
    ?>
</html>