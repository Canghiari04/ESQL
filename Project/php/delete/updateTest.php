<!DOCTYPE html>
<html>
    <head>
        <?php
            include '../connectionDB.php'
        ?>
    </head>
    <?php 
        $conn = openConnection();
        //$manager = openConnectionMongoDB();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['btnUpdateTest'])) {
                
                updateTest($conn, $TestTitle = $_POST['btnUpdateTest']);

                header('Location: ../test.php');
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

        closeConnection($conn);
    ?>
</html>