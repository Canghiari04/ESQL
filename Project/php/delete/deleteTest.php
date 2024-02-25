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
            if(isset($_POST['btnDropTest'])) {
                echo 'yo';
                deleteTest($conn, $TestTitle = $_POST['btnDropTest']);

                header('Location: ../test.php');
            } 
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

        closeConnection($conn);
    ?>
</html>